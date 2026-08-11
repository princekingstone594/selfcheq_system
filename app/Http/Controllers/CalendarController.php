<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Routine;
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
}