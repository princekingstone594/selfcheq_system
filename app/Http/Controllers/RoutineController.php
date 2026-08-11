<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine;
use App\Models\Task;
use App\Models\Financial;
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

        // 🔄 Materialize recurring financial routines (weekly, monthly, quarterly, annually)
        // based on the financial records, not on previously-created routine instances.
        $this->materializeFinancialRoutines($user, $today);

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

    /**
     * Materialize due recurring financial routines (and their linked tasks) for today.
     */
    protected function materializeFinancialRoutines($user, string $today): void
    {
        $frequencies = ['weekly', 'monthly', 'quarterly', 'annually'];

        // Gather all active financial records that have a recurring frequency
        $financials = Financial::where('user_id', $user->id)
            ->whereIn('type', ['tithe', 'bill', 'saving'])
            ->where('is_completed', false)
            ->whereIn('frequency', $frequencies)
            ->get();

        foreach ($financials as $financial) {
            // Find the latest materialized routine for this financial
            $latest = $user->routines()
                ->where('reference_id', $financial->id)
                ->where('reference_type', $financial->type === 'tithe' ? 'tithe' : ($financial->type === 'bill' ? 'bill' : 'saving'))
                ->orderByDesc('date')
                ->first();

            // The routine's date is reminder_days before the due date. We store the
            // routine on its reminder date, so we need to figure out the next due
            // occurrence based on the financial's frequency.
            $dueDate = $latest
                ? $this->nextOccurrenceAfter(Carbon::parse($financial->due_date), $financial->frequency, $latest->date)
                : Carbon::parse($financial->due_date);

            // If no due date is set, fall back to today
            if (!$dueDate) {
                $dueDate = Carbon::today();
            }

            // Compute the reminder date (routine date) from the due date
            $reminderDate = $dueDate->copy();
            if ($financial->reminder_days && $financial->reminder_days > 0) {
                $reminderDate = $reminderDate->subDays($financial->reminder_days);
            }

            // Only materialize if the reminder date is today
            if ($reminderDate->toDateString() !== $today) {
                continue;
            }

            // Build the routine title based on type
            $title = match ($financial->type) {
                'tithe'  => 'Tithe Reminder: ' . ($financial->title ?: 'Tithe Payment'),
                'bill'   => 'Bill Reminder: ' . $financial->title,
                'saving' => 'Saving: ' . $financial->title,
                default  => $financial->title,
            };

            // Check if a routine for today already exists (avoid duplicates)
            $existsToday = $user->routines()
                ->where('reference_id', $financial->id)
                ->where('reference_type', $financial->type === 'tithe' ? 'tithe' : ($financial->type === 'bill' ? 'bill' : 'saving'))
                ->whereDate('date', $today)
                ->exists();

            if (!$existsToday) {
                $user->routines()->create([
                    'title' => $title,
                    'description' => $financial->description,
                    'date' => $today,
                    'is_completed' => false,
                    'reminder_time' => '08:00:00',
                    'frequency' => $financial->frequency,
                    'reference_id' => $financial->id,
                    'reference_type' => $financial->type === 'tithe' ? 'tithe' : ($financial->type === 'bill' ? 'bill' : 'saving'),
                ]);
            }

            // Also materialize a linked Task so it shows in the Tasks section
            $taskTitle = match ($financial->type) {
                'tithe'  => 'Tithe Reminder: ' . ($financial->title ?: 'Tithe Payment'),
                'bill'   => 'Bill Reminder: ' . $financial->title,
                'saving' => 'Saving: ' . $financial->title,
                default  => $financial->title,
            };

            $taskExistsToday = Task::where('user_id', $user->id)
                ->where('reference_id', $financial->id)
                ->where('type', $financial->type === 'saving' ? 'saving' : $financial->type)
                ->whereDate('due_date', $today)
                ->exists();

            if (!$taskExistsToday) {
                Task::create([
                    'user_id' => $user->id,
                    'title' => $taskTitle,
                    'due_date' => $today,
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => $financial->type === 'saving' ? 'saving' : $financial->type,
                    'reference_id' => $financial->id,
                    'frequency' => $financial->frequency,
                ]);
            }
        }
    }

    /**
     * Compute the next occurrence date of a recurring item after a given reference date.
     */
    protected function nextOccurrenceAfter(Carbon $dueDate, string $frequency, string $referenceDate): ?Carbon
    {
        $ref = Carbon::parse($referenceDate);
        $next = $dueDate->copy();

        // Advance until strictly after the reference date
        $guard = 0;
        while ($next->lte($ref) && $guard < 1000) {
            switch ($frequency) {
                case 'weekly':
                    $next->addWeek();
                    break;
                case 'monthly':
                    $next->addMonth();
                    break;
                case 'quarterly':
                    $next->addMonths(3);
                    break;
                case 'annually':
                    $next->addYear();
                    break;
                default:
                    return $dueDate;
            }
            $guard++;
        }

        return $next->gt($ref) ? $next : null;
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
            Task::where('reference_id', $routine->reference_id)
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