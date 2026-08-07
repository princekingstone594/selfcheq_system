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
        $week = $request->input('week', 0);  // 0 = current week
        $start = Carbon::now()->startOfWeek()->addWeeks($week);
        $end = $start->copy()->endOfWeek();

        // Appointments in this week
        $appointments = $user->appointments()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy(function ($a) {
                return Carbon::parse($a->date)->toDateString();
            });

        // Tasks in this week (future or due this week)
        $tasks = $user->tasks()
            ->whereDate('due_date', '>=', $start->toDateString())
            ->whereDate('due_date', '<=', $end->toDateString())
            ->orderBy('due_date')
            ->get()
            ->groupBy(function ($t) {
                return Carbon::parse($t->due_date)->toDateString();
            });

        // Build calendar days
        $days = collect();
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $days->push($day);
        }

        $prevWeek = $week - 1;
        $nextWeek = $week + 1;

        return view('calendar.index', compact(
            'days',
            'appointments',
            'tasks',
            'start',
            'end',
            'week',
            'prevWeek',
            'nextWeek'
        ));
    }
}
