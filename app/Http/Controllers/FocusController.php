<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GamificationService;
use App\Models\FocusSession;
use App\Models\Task;
use App\Models\Routine;
use App\Models\Appointment;
use App\Models\Financial;
use Carbon\Carbon;

class FocusController extends Controller
{
    public function index()
    {
        $sessions = Auth::user()->focusSessions()
            ->latest()
            ->take(5)
            ->get();

        return view('focus.index', compact('sessions'));
    }

    /**
     * Today's Focus — a summary of everything happening on a specific day.
     */
    public function today(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date', now()->toDateString());
        $day = Carbon::parse($date);

        // ✅ Tasks for the day
        $tasks = $user->tasks()
            ->whereDate('due_date', $date)
            ->orderBy('reminder_time')
            ->get();

        // 🔁 Routines for the day (materialized + permanent frequency-based)
        $routines = $user->routines()
            ->whereDate('date', $date)
            ->get();

        // 🔒 Merge in permanent routines that are active on this day
        // Exclude financial-linked routines (reference_id) — those are handled separately.
        $permanentRoutines = $user->routines()
            ->where('is_permanent', true)
            ->whereNull('reference_id')
            ->get()
            ->filter(function ($routine) use ($day) {
                return $routine->isActiveOn($day->toDateString());
            });

        // Avoid duplicates by title
        $existingTitles = $routines->pluck('title')->map(fn($t) => strtolower(trim($t)))->toArray();
        foreach ($permanentRoutines as $permanent) {
            $key = strtolower(trim($permanent->title));
            if (!in_array($key, $existingTitles)) {
                $routines->push($permanent);
                $existingTitles[] = $key;
            }
        }

        // Sort routines by reminder time
        $routines = $routines->sortBy(function ($r) {
            return $r->reminder_time ? \Carbon\Carbon::parse($r->reminder_time)->format('H:i') : '99:99';
        })->values();

        // ⏰ Appointments for the day
        $appointments = $user->appointments()
            ->whereDate('date', $date)
            ->orderBy('time')
            ->get();

        // 💰 Financial reminders for the day (due on this date)
        $financials = $user->financials()
            ->whereDate('due_date', $date)
            ->get();

        // 🧠 Focus sessions for the day
        $focusMinutes = $user->focusSessions()
            ->whereDate('started_at', $date)
            ->sum('duration');

        // 📓 Journal for the day
        $journal = $user->journals()
            ->whereDate('date', $date)
            ->first();

        // 📊 Summary counts
        $totalItems = $tasks->count() + $routines->count() + $appointments->count() + $financials->count();
        $completedItems = $tasks->where('is_completed', true)->count()
            + $routines->where('is_completed', true)->count()
            + $appointments->where('is_completed', true)->count()
            + $financials->where('is_completed', true)->count();

        return view('focus.today', compact(
            'tasks',
            'routines',
            'appointments',
            'financials',
            'focusMinutes',
            'journal',
            'date',
            'day',
            'totalItems',
            'completedItems'
        ));
    }

    public function start(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:180',
        ]);

        $session = FocusSession::create([
            'user_id' => Auth::id(),
            'duration' => $request->duration,
            'started_at' => now(),
        ]);

        $user = Auth::user();

        // Award XP: 1 XP per minute, capped at 60 XP per session
        $xp = min($request->duration, 60);
        GamificationService::awardXp($user, $xp, "Focus session: {$request->duration} min");
        GamificationService::recordDailyActivity($user);

        return back();
    }

    public function stop(FocusSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->update([
            'ended_at' => now(),
        ]);

        return back();
    }
}