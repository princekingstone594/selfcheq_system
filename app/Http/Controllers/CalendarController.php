<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Routine;
use App\Models\Appointment;
use App\Models\Financial;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'week');

        $prevMonth = null;
        $nextMonth = null;
        $prevWeek = null;
        $nextWeek = null;

        if ($view === 'month') {
            // Month view
            $month = $request->input('month', now()->format('Y-m'));
            [$year, $monthNum] = explode('-', $month);

            $start = Carbon::createFromDate($year, $monthNum, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            // Get the first day of the week containing the 1st
            $calendarStart = $start->copy()->startOfWeek();
            // Get the last day of the week containing the last day
            $calendarEnd = $end->copy()->endOfWeek();

            $prevMonth = $start->copy()->subMonth()->format('Y-m');
            $nextMonth = $start->copy()->addMonth()->format('Y-m');

            // Build all calendar days (including padding days)
            $days = collect();
            $current = $calendarStart->copy();
            while ($current->lt($calendarEnd) || $current->eq($calendarEnd)) {
                $days->push($current->copy());
                $current->addDay();
            }
        } else {
            // Week view
            $week = $request->input('week', 0);
            $start = Carbon::now()->startOfWeek()->addWeeks($week);
            $end = $start->copy()->endOfWeek();

            $days = collect();
            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $days->push($day);
            }

            $prevWeek = $week - 1;
            $nextWeek = $week + 1;
        }

        // Appointments
        $appointments = $user->appointments()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy(function ($a) {
                return Carbon::parse($a->date)->toDateString();
            });

        // Tasks (materialized for this period)
        $tasks = $user->tasks()
            ->whereDate('due_date', '>=', $start->toDateString())
            ->whereDate('due_date', '<=', $end->toDateString())
            ->orderBy('due_date')
            ->get()
            ->groupBy(function ($t) {
                return Carbon::parse($t->due_date)->toDateString();
            });

        // Routines (materialized for this period)
        $routines = $user->routines()
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->get()
            ->groupBy(function ($r) {
                return Carbon::parse($r->date)->toDateString();
            });

        // 🔁 Project permanent routines (is_permanent = true) into the visible period
        // based on their frequency (weekday, weekend, daily, weekly, monthly, etc.)
        // Exclude financial-linked routines (reference_id) — those are handled separately.
        $permanentRoutines = $user->routines()
            ->where('is_permanent', true)
            ->whereNull('reference_id')
            ->get();

        // Materialize for each day in the visible period
        foreach ($days as $day) {
            $dayStr = $day->toDateString();

            foreach ($permanentRoutines as $permRoutine) {
                // Check if this permanent routine is active on this day
                if (!$permRoutine->isActiveOn($dayStr)) {
                    continue;
                }

                // Check if a materialized routine already exists for this day+title
                $exists = isset($routines[$dayStr])
                    && $routines[$dayStr]->contains(function ($r) use ($permRoutine) {
                        return strtolower(trim($r->title)) === strtolower(trim($permRoutine->title));
                    });

                if (!$exists) {
                    if (!isset($routines[$dayStr])) {
                        $routines[$dayStr] = collect();
                    }
                    $routines[$dayStr]->push((object) [
                        'title' => $permRoutine->title,
                        'date' => $day,
                        'description' => $permRoutine->description,
                        'is_completed' => false,
                        'frequency' => $permRoutine->frequency,
                        'reminder_time' => $permRoutine->reminder_time,
                        'is_projected' => true,
                        'is_permanent' => true,
                    ]);
                }
            }
        }

        // 🔄 Project future recurring financial routines & tasks into this period
        // so the calendar shows upcoming recurring items even if not yet materialized in the DB.
        $recurringFinancials = Financial::where('user_id', $user->id)
            ->whereIn('type', ['tithe', 'bill', 'saving'])
            ->where('is_completed', false)
            ->whereIn('frequency', ['weekly', 'monthly', 'quarterly', 'annually'])
            ->get();

        foreach ($recurringFinancials as $financial) {
            $baseDue = $financial->due_date ? Carbon::parse($financial->due_date) : Carbon::today();
            $reminderDays = $financial->reminder_days ?? 0;
            $freq = $financial->frequency;

            // Find the latest materialized occurrence to avoid re-projecting past dates
            $latestRoutine = $user->routines()
                ->where('reference_id', $financial->id)
                ->orderByDesc('date')
                ->first();

            // Advance from the base due date generating occurrences within the period
            $occurrence = $baseDue->copy();
            $guard = 0;
            while ($occurrence->lt($end) && $guard < 1000) {
                $guard++;

                // Compute the reminder (routine) date
                $reminderDate = $occurrence->copy()->subDays($reminderDays);

                // Only project if within the visible period
                if ($reminderDate->between($start, $end)) {
                    $dayStr = $reminderDate->toDateString();

                    $title = match ($financial->type) {
                        'tithe'  => 'Tithe Reminder: ' . ($financial->title ?: 'Tithe Payment'),
                        'bill'   => 'Bill Reminder: ' . $financial->title,
                        'saving' => 'Saving: ' . $financial->title,
                        default  => $financial->title,
                    };

                    // Merge into routines collection (as lightweight objects)
                    if (!isset($routines[$dayStr])) {
                        $routines[$dayStr] = collect();
                    }
                    $routines[$dayStr]->push((object) [
                        'title' => $title,
                        'date' => $reminderDate,
                        'is_completed' => false,
                        'frequency' => $freq,
                        'reference_id' => $financial->id,
                        'reference_type' => $financial->type === 'tithe' ? 'tithe' : ($financial->type === 'bill' ? 'bill' : 'saving'),
                        'is_projected' => true,
                    ]);

                    // Also project a linked task for the reminder date
                    if (!isset($tasks[$dayStr])) {
                        $tasks[$dayStr] = collect();
                    }
                    $tasks[$dayStr]->push((object) [
                        'title' => $title,
                        'due_date' => $reminderDate,
                        'is_completed' => false,
                        'frequency' => $freq,
                        'reference_id' => $financial->id,
                        'is_projected' => true,
                    ]);
                }

                // Advance to next occurrence
                switch ($freq) {
                    case 'weekly':
                        $occurrence->addWeek();
                        break;
                    case 'monthly':
                        $occurrence->addMonth();
                        break;
                    case 'quarterly':
                        $occurrence->addMonths(3);
                        break;
                    case 'annually':
                        $occurrence->addYear();
                        break;
                }
            }
        }

        return view('calendar.index', compact(
            'days',
            'appointments',
            'tasks',
            'routines',
            'start',
            'end',
            'view',
            'prevWeek',
            'nextWeek',
            'prevMonth',
            'nextMonth'
        ));
    }

    /**
     * Quick-add a task, routine, or appointment from the calendar.
     */
    public function quickAdd(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $type = $request->input('type');
        $date = $request->input('date', now()->toDateString());

        $request->validate([
            'type' => 'required|in:task,routine,appointment',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable',
            'reminder_time' => 'nullable|date_format:H:i',
            'frequency' => 'nullable|in:weekday,weekend,daily,once,weekly,monthly,quarterly,annually',
            'notes' => 'nullable|string',
        ]);

        switch ($type) {
            case 'task':
                Task::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'due_date' => Carbon::parse($date)->toDateString(),
                    'reminder_time' => $request->reminder_time,
                    'alarm_enabled' => $request->has('alarm_enabled'),
                    'alarm_time' => $request->reminder_time,
                    'is_important' => $request->has('is_important'),
                    'is_urgent' => $request->has('is_urgent'),
                ]);
                $message = 'Task added for ' . Carbon::parse($date)->format('D, d M') . '! ✅';
                break;

            case 'routine':
                $frequency = $request->frequency ?? 'daily';
                $isPermanent = $frequency !== 'once';

                Routine::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'description' => $request->notes,
                    'date' => Carbon::parse($date)->toDateString(),
                    'is_completed' => false,
                    'reminder_time' => $request->reminder_time,
                    'frequency' => $frequency,
                    'is_permanent' => $isPermanent,
                ]);
                $message = $isPermanent
                    ? 'Permanent routine added! It will stay until you delete it. 🔒'
                    : 'Routine added for ' . Carbon::parse($date)->format('d M') . '! 🔁';
                break;

            case 'appointment':
                Appointment::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'time' => $request->time,
                    'date' => Carbon::parse($date)->toDateString(),
                    'notes' => $request->notes,
                ]);
                $message = 'Appointment added to ' . Carbon::parse($date)->format('d M') . '! 🗓️';
                break;

            default:
                abort(400);
        }

        return back()->with('success', $message);
    }
}
