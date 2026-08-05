<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDate = $today->toDateString();

        // ✅ Tasks (Today)
        $tasksToday = $user->tasks()->whereDate('due_date', $todayDate)->get();
        $taskTotal = $tasksToday->count();
        $taskCompleted = $tasksToday->where('is_completed', true)->count();

        // 🔁 Routines
        $routines = $user->routines()->whereDate('date', $todayDate)->get();
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        // ⏰ Focus
        $focusMinutes = $user->focusSessions()
            ->whereDate('started_at', $todayDate)
            ->sum('duration');

        // 📓 Journal
        $journalExists = $user->journals()
            ->whereDate('date', $todayDate)
            ->exists();

        // 😊 Mood average (last 7 days)
        $moodAvg = $user->journals()
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->avg('mood');
        $moodAvg = $moodAvg ? round($moodAvg, 1) : null;

        // 🎯 Discipline Score
        $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;
        $routineScore = $routineTotal > 0 ? ($routineCompleted / $routineTotal) * 20 : 0;
        $journalScore = $journalExists ? 20 : 0;
        $focusScore = min($focusMinutes / 60, 1) * 20;

        $disciplineScore = round(
            $taskScore + $routineScore + $journalScore + $focusScore
        );

        // 📊 Charts (last 7 days)
        $taskChart = [];
        $taskLabels = [];
        $moodChart = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $taskLabels[] = $date->format('D');

            $taskChart[] = $user->tasks()
                ->whereDate('due_date', $date)
                ->where('is_completed', true)
                ->count();

            $moodChart[] = $user->journals()
                ->whereDate('date', $date)
                ->avg('mood') ?? 0;
        }

        // 🎂 Birthdays (safe)
        $birthdays = ($user->contacts ?? collect())->filter(function ($contact) use ($today) {
            return $contact->birthday &&
                Carbon::parse($contact->birthday)->isSameDay($today);
        });

        // 🔔 Nudges
        $nudges = [];

        if ($taskTotal > $taskCompleted) {
            $nudges[] = "Finish your remaining tasks 💪";
        }

        if (!$journalExists) {
            $nudges[] = "Write your journal 🧠";
        }

        if ($focusMinutes < 30) {
            $nudges[] = "Do a 30min focus session ⏳";
        }

        if (empty($nudges)) {
            $nudges[] = "You're doing amazing today 🌟";
        }

        return view('progress.index', compact(
            'disciplineScore',
            'focusMinutes',
            'moodAvg',
            'taskChart',
            'taskLabels',
            'moodChart',
            'nudges',
            'birthdays'
        ));
    }
}