<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 🔁 Routines for the day (materialized + frequency-based)
        $routines = $user->routines()
            ->whereDate('date', $date)
            ->get();

        // If no routines materialized for this date, project from frequency templates
        if ($routines->isEmpty()) {
            $allRoutines = $user->routines()
                ->whereNotNull('frequency')
                ->whereNull('reference_id')
                ->get()
                ->filter(function ($routine) use ($day) {
                    return $routine->isActiveOn($day->toDateString());
                });
            $routines = $allRoutines->values();
        }

        // ⏰ Appointments for the day
        $appointments = $user->appointments()
            ->whereDate('date', $date)
            ->orderBy('time')
            ->get();

        // 💰 Financial reminders for the day (due today or reminder today)
        $financials = $user->financials()
            ->whereDate('due_date', $date)
            ->where('is_completed', false)
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

        FocusSession::create([
            'user_id' => Auth::id(),
            'duration' => $request->duration,
            'started_at' => now(),
        ]);

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