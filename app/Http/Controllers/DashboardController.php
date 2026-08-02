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

        // 📊 Tasks per day (last 7 days)
        $taskChart = [];
        $taskLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $taskLabels[] = $date->format('D');

            $taskChart[] = $user->tasks()
                ->whereDate('due_date', $date)
                ->where('is_completed', true)
                ->count();
        }

        // 😊 Mood trend (last 7 days)
        $moodChart = [];

        for ($i = 6; $i >=0; $i--) {
            $date = Carbon::now()->subDays($i);

            $mood = $user->journals()
                ->whereDate('date', $date)
                ->avg('mood');

            $moodChart[] = $mood;       
        }

        // 🎯 Discipline Score

        $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;

        $routineScore = $routineTotal > 0
            ? ($routineCompleted / $routineTotal) * 20 
            : 0;

        $journalScore = $journalExists ? 20 : 0;

        $focusScore = min($focusMinutes / 60, 1) * 20; // Max 60 mins = full score

        $disciplineScore = round(
            $taskScore + $routineScore + $journalScore + $focusScore);

        $nudges = [];

        // Tasks nudge
        if ($taskTotal > 0 && $taskCompleted < $taskTotal) {
            $nudges[] = "You're close to completing your tasks. Finish strong! 💪";
        }

        // Journal nudge
        if (!$journalExists) {
            $nudges[] = "Take 2 minutes to reflect and journal your day 🧠";
        }

        // Focus nudge
        if ($focusMinutes < 30) {
            $nudges[] = "Try a 30-minute focus session to boost your productivity! ⌛";
        }

        // Empty fallback
        if (empty($nudges)) {
            $nudges[] = "Great job today! Keep up the good work! 🌟";
        }

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
            'weeklyTasks',
            'taskChart',
            'taskLabels',
            'moodChart',
            'disciplineScore',
            'nudges',
        ));
    }
}