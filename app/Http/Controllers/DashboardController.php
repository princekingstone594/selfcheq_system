<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        // ✅ Tasks (Today)
        $tasks = $user->tasks()->whereDate('due_date', $today)->get();
        $taskTotal = $tasks->count();
        $taskCompleted = $tasks->where('is_completed', true)->count();
        $taskProgress = $taskTotal > 0 
            ? round(($taskCompleted / $taskTotal) * 100) 
            : 0;

        // 🔁 Routines
        $routines = $user->routines()->whereDate('date', $today)->get();
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        // ⏰ Appointments
        $appointments = $user->appointments()
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        // 🧠 Focus (today total minutes)
        $focusMinutes = $user->focusSessions()
            ->whereDate('started_at', $today)
            ->sum('duration');

        // 📓 Journal (today)
        $journalExists = $user->journals()
            ->whereDate('date', $today)
            ->exists();

        // 😊 Mood average (last 7 days)
        $moodAvg = $user->journals()
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->avg('mood');

        $moodAvg = $moodAvg ? round($moodAvg, 1) : null;

        // 📊 Weekly completed tasks
        $weeklyTasks = $user->tasks()
            ->where('due_date', '>=', Carbon::now()->subDays(7))
            ->where('is_completed', true)
            ->count();

        return view('dashboard', compact(
            'taskTotal',
            'taskCompleted',
            'taskProgress',
            'routineCompleted',
            'routineTotal',
            'appointments',
            'focusMinutes',
            'journalExists',
            'moodAvg',
            'weeklyTasks'
        ));
    }
}