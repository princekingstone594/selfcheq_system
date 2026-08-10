<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Tasks
        $tasks = $user->tasks()
            ->whereDate('due_date', '>=', $start->toDateString())
            ->whereDate('due_date', '<=', $end->toDateString())
            ->orderBy('due_date')
            ->get()
            ->groupBy(function ($t) {
                return Carbon::parse($t->due_date)->toDateString();
            });

        return view('calendar.index', compact(
            'days',
            'appointments',
            'tasks',
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