<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine;
use Carbon\Carbon;

class RoutineController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // If no routines today → copy from yesterday (respecting frequency)
        $exists = $user->routines()->whereDate('date', $today)->exists();

        if (!$exists) {
            $yesterday = now()->subDay()->toDateString();

            $yesterdayRoutines = $user->routines()
                ->whereDate('date', $yesterday)
                ->get();

            foreach ($yesterdayRoutines as $routine) {
                // Only carry over if the routine's frequency is active today
                if ($routine->isActiveOn($today)) {
                    $user->routines()->create([
                        'title' => $routine->title,
                        'description' => $routine->description,
                        'date' => $today,
                        'is_completed' => false,
                        'reminder_time' => $routine->reminder_time?->format('H:i'),
                        'frequency' => $routine->frequency ?? 'daily',
                    ]);
                }
            }
        }

        $routines = $user->routines()
            ->whereDate('date', $today)
            ->get();

        // 📜 History (past routines, excluding today, grouped by date)
        $history = $user->routines()
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(function ($r) {
                return Carbon::parse($r->date)->format('Y-m-d');
            });

        // 🎯 Templates (routines with frequency set, stored as date=null templates)
        $templates = $user->routines()
            ->whereNull('date')
            ->get();

        return view('routines.index', compact('routines', 'history', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_time' => 'nullable|date_format:H:i',
            'frequency' => 'nullable|in:weekday,weekend,daily,once',
        ]);

        $frequency = $request->frequency ?? 'daily';

        // Extra (one-off) routines get today's date
        // Template routines (weekday/weekend/daily) also get today's date initially —
        // they'll be carried forward daily based on frequency
        $routine = Auth::user()->routines()->create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => now()->toDateString(),
            'user_id' => auth()->id(),
            'is_completed' => false,
            'reminder_time' => $request->reminder_time,
            'frequency' => $frequency,
        ]);

        $timeMsg = $routine->reminder_time
            ? " ⏰ Alarm at {$routine->formatted_alarm_time}"
            : '';

        $msg = $routine->frequency === 'once'
            ? "Extra routine added!{$timeMsg}"
            : "Routine created with alarm!{$timeMsg}";

        return redirect()->back()->with('success', $msg);
    }

    public function toggle(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $routine->update([
            'is_completed' => !$routine->is_completed
        ]);

        return back()->with('success', $routine->is_completed
            ? '✅ Routine completed — another step toward discipline!'
            : '🔄 Routine marked incomplete. Keep going!');
    }

    public function destroy(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $routine->delete();

        return back()->with('success', 'Routine removed. 🗑️');
    }
}
