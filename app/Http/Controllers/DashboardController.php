<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AiCoachService;
use Illuminate\Support\Facades\Cache;
use App\Models\DailyStat;

class DashboardController extends Controller
{
    public function index(AiCoachService $aiCoach)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDate = $today->toDateString();

        // ✅ Tasks (Today)
        $tasksToday = $user->tasks()->whereDate('due_date', $todayDate)->get();
        $taskTotal = $tasksToday->count();
        $taskCompleted = $tasksToday->where('is_completed', true)->count();

        $taskProgress = $taskTotal > 0
            ? round(($taskCompleted / $taskTotal) * 100)
            : 0;

        // 🔁 Routines
        $routines = $user->routines()->whereDate('date', $todayDate)->get();
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        // ⏰ Appointments
        $appointments = $user->appointments()
            ->whereDate('date', $todayDate)
            ->orderBy('time')
            ->get();

        // 🧠 Focus
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

        // 📊 Weekly completed tasks
        $weeklyTasks = $user->tasks()
            ->where('due_date', '>=', Carbon::now()->subDays(7))
            ->where('is_completed', true)
            ->count();

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

        // 🎯 Discipline Score
        $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;
        $routineScore = $routineTotal > 0 ? ($routineCompleted / $routineTotal) * 20 : 0;
        $journalScore = $journalExists ? 20 : 0;
        $focusScore = min($focusMinutes / 60, 1) * 20;

        $disciplineScore = round(
            $taskScore + $routineScore + $journalScore + $focusScore
        );

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

        // 🎂 Birthdays (safe)
        $birthdays = ($user->contacts ?? collect())->filter(function ($contact) use ($today) {
            return $contact->birthday &&
                Carbon::parse($contact->birthday)->isSameDay($today);
        });

        // 📊 Eisenhower Matrix
        $allTasks = $user->tasks()->whereDate('created_at', $todayDate)->get();

        $doNow = $allTasks->where('is_important', true)->where('is_urgent', true);
        $schedule = $allTasks->where('is_important', true)->where('is_urgent', false);
        $delegate = $allTasks->where('is_important', false)->where('is_urgent', true);
        $eliminate = $allTasks->where('is_important', false)->where('is_urgent', false);

        // 🤖 AI Coach
        $data = [
            'score' => $disciplineScore,
            'tasks_completed' => $taskCompleted,
            'tasks_total' => $taskTotal,
            'focus' => $focusMinutes,
            'journal' => $journalExists,
            'mode' => $user->coach_mode,
        ];

        $coachMessage = Cache::remember(
            'ai_coach_' . $user->id . '_' . $todayDate,
            3600,
            function () use ($aiCoach, $data) {
                try {
                    return $aiCoach->generate($data);
                } catch (\Exception $e) {
                    return "Stay consistent. Small daily wins build greatness 💪";
                }
            }
        );

        // 📊 History (last 7)
        $history = DailyStat::where('user_id', $user->id)
            ->latest('date')
            ->take(7)
            ->get();

        // 💾 Save today's stats
        DailyStat::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $todayDate,
            ],
            [
                'score' => $disciplineScore,
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus_minutes' => $focusMinutes,
                'journaled' => $journalExists,
            ]
        );

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
            'birthdays',
            'doNow',
            'schedule',
            'delegate',
            'eliminate',
            'coachMessage'
        ));
    }
}