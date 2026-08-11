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
                        'reference_id' => $routine->reference_id,
                        'reference_type' => $routine->reference_type,
                    ]);
                }
            }
        }

        // 🔄 Carry forward recurring financial routines (weekly, monthly, quarterly, annually)
        $recurringFrequencies = ['weekly', 'monthly', 'quarterly', 'annually'];

        $financialRoutines = $user->routines()
            ->whereIn('frequency', $recurringFrequencies)
            ->whereNotNull('reference_id')
            ->get()
            ->groupBy(function ($r) {
                return $r->reference_id . ':' . ($r->reference_type ?? '');
            });

        foreach ($financialRoutines as $groupKey => $group) {
            $latest = $group->sortByDesc('date')->first();
            $latestDate = Carbon::parse($latest->date);

            // Determine if a new instance is due today
            $shouldCreate = false;
            switch ($latest->frequency) {
                case 'weekly':
                    $shouldCreate = $latestDate->copy()->addWeek()->isPast();
                    break;
                case 'monthly':
                    $shouldCreate = $latestDate->copy()->addMonth()->isPast();
                    break;
                case 'quarterly':
                    $shouldCreate = $latestDate->copy()->addMonths(3)->isPast();
                    break;
                case 'annually':
                    $shouldCreate = $latestDate->copy()->addYear()->isPast();
                    break;
            }

            if ($shouldCreate) {
                // Check if an instance for today already exists in this group
                $existsToday = $group->contains(function ($r) use ($today) {
                    return Carbon::parse($r->date)->toDateString() === $today;
                });

                if (!$existsToday) {
                    $user->routines()->create([
                        'title' => $latest->title,
                        'description' => $latest->description,
                        'date' => $today,
                        'is_completed' => false,
                        'reminder_time' => $latest->reminder_time?->format('H:i'),
                        'frequency' => $latest->frequency,
                        'reference_id' => $latest->reference_id,
                        'reference_type' => $latest->reference_type,
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
            'frequency' => 'nullable|in:weekday,weekend,daily,once,weekly,monthly,quarterly,annually',
        ]);

        $frequency = $request->frequency ?? 'daily';

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

        // If this routine is tied to a financial, also clean up the financial's
        // associated tasks and other routine instances
        if ($routine->reference_id) {
            // Clean up associated tasks for this financial
            \App\Models\Task::where('reference_id', $routine->reference_id)
                ->whereIn('type', ['tithe', 'bill', 'saving', 'saving_target'])
                ->delete();

            // Clean up all routine instances for this financial
            Routine::where('reference_id', $routine->reference_id)
                ->where('reference_type', $routine->reference_type)
                ->delete();
        } else {
            $routine->delete();
        }

        return back()->with('success', 'Routine removed. 🗑️');
    }
}
