<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Tasks
        $tasks = $user->tasks()->whereDate('due_date', $today)->get();
        $taskTotal = $tasks->count();
        $taskCompleted = $tasks->where('is_completed', true)->count();
        $taskProgress = $taskTotal > 0 ? round(($taskCompleted / $taskTotal) * 100) : 0;

        // Routines
        $routines = $user->routines()->whereDate('date', $today)->get();
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        // Appointments
        $appointments = $user->appointments()
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        // Focus (today total minutes)
        $focusMinutes = $user->focusSessions()
            ->whereDate('started_at', $today)
            ->sum('duration');

        // Journal
        $journalExists = $user->journals()
            ->whereDate('date', $today)
            ->exists();

        return view('dashboard', compact(
            'taskTotal',
            'taskCompleted',
            'taskProgress',
            'routineCompleted',
            'routineTotal',
            'appointments',
            'focusMinutes',
            'journalExists'
        ));
    }
}