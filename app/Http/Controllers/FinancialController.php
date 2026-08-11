<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use App\Models\Task;
use App\Models\Routine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $financials = $user->financials()->latest()->get();

        // Separate by type
        $bills = $financials->where('type', 'bill');
        $tithers = $financials->where('type', 'tithe');
        $savings = $financials->where('type', 'saving');

        // History (completed items)
        $completed = $financials->where('is_completed', true);

        return view('financials.index', compact(
            'financials',
            'bills',
            'tithers',
            'savings',
            'completed'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type');

        if ($type === 'tithe') {
            $validated = $request->validate([
                'type' => 'required|in:saving,tithe,bill',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'frequency' => 'required|in:weekly,monthly',
                'due_date' => 'nullable|integer|min:1|max:31',
                'reminder_days' => 'nullable|integer|min:0|max:30',
                'is_recurring' => 'boolean',
            ]);

            // Tithes are inherently recurring — always create the recurring routine
            $validated['is_recurring'] = true;

            // Convert day-of-month to actual date (next occurrence of that day)
            $dayOfMonth = (int)($validated['due_date'] ?? 10);
            $now = Carbon::now();

            // Build the date for this month's chosen day, clamped to month-end if needed
            $nextOccurrence = $now->copy()->startOfMonth()->addDays(min($dayOfMonth, $now->daysInMonth) - 1);
            if ($dayOfMonth > $now->daysInMonth) {
                $nextOccurrence = $nextOccurrence->endOfMonth();
            }

            // If it's already past, roll forward to next month (and re-clamp)
            if ($nextOccurrence->isPast()) {
                $nextMonth = $now->copy()->addMonth()->startOfMonth();
                $nextOccurrence = $nextMonth->addDays(min($dayOfMonth, $nextMonth->daysInMonth) - 1);
                if ($dayOfMonth > $nextMonth->daysInMonth) {
                    $nextOccurrence = $nextMonth->endOfMonth();
                }
            }
            $validated['due_date'] = $nextOccurrence->toDateString();

            $financial = $request->user()->financials()->create($validated);

            // Create a recurring Routine for the tithe reminder
            $reminderDays = $financial->reminder_days ?? 3;
            $routineDate = Carbon::parse($financial->due_date);
            if ($reminderDays > 0) {
                $routineDate = $routineDate->copy()->subDays($reminderDays);
            }
            $frequency = $financial->frequency === 'weekly' ? 'weekly' : 'monthly';

            Routine::create([
                'user_id' => $financial->user_id,
                'title' => 'Tithe Reminder: ' . ($financial->title ?: 'Tithe Payment'),
                'description' => $financial->description,
                'date' => $routineDate->toDateString(),
                'is_completed' => false,
                'reminder_time' => '08:00:00',
                'frequency' => $frequency,
                'reference_id' => $financial->id,
                'reference_type' => 'tithe',
            ]);

            // Also create a one-time Task reminder
            if ($reminderDays > 0) {
                $reminderDate = Carbon::parse($financial->due_date)->subDays($reminderDays);
                // Don't duplicate if the routine covers it
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Tithe Reminder: ' . ($financial->title ?: 'Tithe Payment'),
                    'due_date' => $reminderDate,
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'tithe',
                    'reference_id' => $financial->id,
                    'frequency' => $frequency,
                ]);
            }
        } elseif ($type === 'bill') {
            $validated = $request->validate([
                'type' => 'required|in:saving,tithe,bill',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'frequency' => 'nullable|in:weekly,monthly,one-time,quarterly,annually',
                'due_date' => 'nullable|date',
                'reminder_days' => 'nullable|integer|min:0',
                'is_recurring' => 'boolean',
            ]);

            // If the bill frequency implies recurrence, treat it as recurring
            if (in_array($validated['frequency'] ?? null, ['weekly', 'monthly', 'quarterly', 'annually'])) {
                $validated['is_recurring'] = true;
            }

            $financial = $request->user()->financials()->create($validated);

            // Recurring bill → create a Routine that repeats
            if ($financial->is_recurring && $financial->frequency && $financial->frequency !== 'one-time') {
                $routineDate = Carbon::parse($financial->due_date);
                if ($financial->reminder_days > 0) {
                    $routineDate = $routineDate->copy()->subDays($financial->reminder_days);
                }

                Routine::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Bill Reminder: ' . $financial->title,
                    'description' => $financial->description,
                    'date' => $routineDate->toDateString(),
                    'is_completed' => false,
                    'reminder_time' => '08:00:00',
                    'frequency' => $financial->frequency,
                    'reference_id' => $financial->id,
                    'reference_type' => 'bill',
                ]);
            }

            // Create a reminder Task (for one-time bills or with reminder days)
            if ($financial->reminder_days && $financial->due_date) {
                $reminderDate = Carbon::parse($financial->due_date)->subDays($financial->reminder_days);
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Bill Reminder: ' . $financial->title,
                    'due_date' => $reminderDate,
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'bill',
                    'reference_id' => $financial->id,
                    'frequency' => $financial->frequency,
                ]);
            }

            // For one-time bills with no reminder_days, still create a task for the due date
            if (!$financial->is_recurring && $financial->due_date && !$financial->reminder_days) {
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Bill Due: ' . $financial->title,
                    'due_date' => Carbon::parse($financial->due_date)->toDateString(),
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'bill',
                    'reference_id' => $financial->id,
                    'frequency' => $financial->frequency,
                ]);
            }
        } elseif ($type === 'saving') {
            $validated = $request->validate([
                'type' => 'required|in:saving,tithe,bill',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'frequency' => 'required|in:one-time,weekly,monthly,quarterly,annually',
                'due_date' => 'nullable|date',
                'reminder_days' => 'nullable|integer|min:0',
                'is_recurring' => 'boolean',
            ]);

            $financial = $request->user()->financials()->create($validated);

            // Create a Routine for recurring savings (weekly/monthly/etc.)
            // This shows up in the task/routines section and repeats based on frequency
            if ($financial->frequency && $financial->frequency !== 'one-time') {
                $dueDate = $financial->due_date ?? Carbon::now()->addWeek();

                // Calculate first reminder date (if reminder_days set, go before due_date)
                $routineDate = Carbon::parse($dueDate);
                if ($financial->reminder_days && $financial->reminder_days > 0) {
                    $routineDate = $routineDate->copy()->subDays($financial->reminder_days);
                }

                Routine::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Saving: ' . $financial->title,
                    'description' => $financial->description,
                    'date' => $routineDate->toDateString(),
                    'is_completed' => false,
                    'reminder_time' => '08:00:00',
                    'frequency' => $financial->frequency,
                    'reference_id' => $financial->id,
                    'reference_type' => 'saving',
                ]);

                // Also create a recurring Task that shows in the tasks section
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Saving: ' . $financial->title,
                    'due_date' => $routineDate->toDateString(),
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'saving',
                    'reference_id' => $financial->id,
                    'frequency' => $financial->frequency,
                ]);
            }

            // Create a one-time Task reminder for the target date
            if ($financial->reminder_days && $financial->due_date) {
                $reminderDate = Carbon::parse($financial->due_date)->subDays($financial->reminder_days);
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Saving Target Reminder: ' . $financial->title,
                    'due_date' => $reminderDate,
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'saving_target',
                    'reference_id' => $financial->id,
                ]);
            }

            // One-time saving without target date — create a simple task
            if ($financial->frequency === 'one-time' && !$financial->due_date) {
                Task::create([
                    'user_id' => $financial->user_id,
                    'title' => 'Saving: ' . $financial->title,
                    'due_date' => Carbon::now()->toDateString(),
                    'reminder_time' => '08:00:00',
                    'alarm_enabled' => true,
                    'is_important' => true,
                    'type' => 'saving',
                    'reference_id' => $financial->id,
                ]);
            }
        }

        return redirect()->route('financials.index')->with('status', 'financial-added');
    }

    public function toggle(Financial $financial): RedirectResponse
    {
        if ($financial->user_id !== Auth::id()) {
            abort(403);
        }

        $financial->update(['is_completed' => !$financial->is_completed]);

        return back()->with('status', 'financial-updated');
    }

    public function destroy(Financial $financial): RedirectResponse
    {
        if ($financial->user_id !== Auth::id()) {
            abort(403);
        }

        // Clean up associated tasks
        Task::where('reference_id', $financial->id)
            ->whereIn('type', ['tithe', 'bill', 'saving', 'saving_target'])
            ->delete();

        // Clean up associated routines
        Routine::where('reference_id', $financial->id)
            ->whereIn('reference_type', ['tithe', 'bill', 'saving'])
            ->delete();

        $financial->delete();

        return back()->with('status', 'financial-deleted');
    }
}
