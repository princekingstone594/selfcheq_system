<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AI\CoachService;
use Illuminate\Support\Facades\Cache;
use App\Models\DailyStat;

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

        $today = Carbon::today();

        $birthdays = auth()->user()->contacts
            ->filter(function ($contact) use ($today) {
                return $contact->birthday && 
                    Carbon::parse($contact->birthday)->isSameDay($today);
            });

        $tasks = auth()->user()->tasks()->whereDate('created_at', today())->get();

        $doNow = $tasks->where('is_important', true)->where('is_urgent', true);
        $schedule = $tasks->where('is_important', true)->where('is_urgent', false);
        $delegate = $tasks->where('is_important', false)->where('is_urgent', true);
        $eliminate = $tasks->where('is_important', false)->where('is_urgent', false); 
        
        $yesterday = Carbon::yesterday();

        $yesterdayTasks = auth()->user()->tasks()
            ->whereDate('created_at', $yesterday)
            ->get();

        $yesterdayCompleted = $yesterdayTasks->where('is_completed', true)->count();
        $yesterdayTotal = $yesterdayTasks->count();

        $coachMessage = "";

        //📉 Low Perfomance
        if ($disciplineScore < 40) {
            $coachMessage = "You had a slow day. Reset tomorrow. start small and build momentum. Remember, consistency is key! 💪";
        }

        //⚖️ Average Performance
        elseif ($disciplineScore < 70) {
            $coachMessage = "You're doing okay, but there's room for improvement. Focus on your priorities and stay consistent. You got this! 🌟";
        }

        //🚀 High Performance
        else {
            $coachMessage = "Fantastic work! You're on a roll. Keep up the great habits and continue to challenge yourself. The sky's the limit! 🚀";
        }

        // Compare with yesterday's performance
        if ($yesterdayTotal > 0) {
            $todayRate = $taskTotal > 0 ? $taskCompleted / $taskTotal : 0;
            $yesterdayRate = $yesterdayCompleted / $yesterdayTotal;

            if ($todayRate > $yesterdayRate) {
                $coachMessage .= " You're improving compared to yesterday! Keep the momentum going! 📈";
            } elseif ($todayRate < $yesterdayRate) {
                $coachMessage .= " You had a slower day compared to yesterday. Don't be discouraged, tomorrow is a new opportunity! 🔄";
            } else {
                $coachMessage .= " Your performance is consistent with yesterday. Keep pushing forward! 💪";
            }
        }

        $ai = new \App\Services\AiCoachService();

        $data = [
            'score' => $disciplineScore,
            'tasks_completed' => $taskCompleted,
            'tasks_total' => $taskTotal,
            'focus' => $focusMinutes,
            'journal' => $journalExists,
        ];

        $coachMessage = Cache::remember(
            'ai_coach_' . auth()->id() . '_' . now()->toDateString(),
            3600, // 1 hour
            function () use ($ai, $data) {
                try {
                    return $ai->generate($data);
                } catch (\Exception $e) {
                    return "Stay consistent. Small daily wins build discipline and compound to success. Keep going! 💪";
                }
            }
        );

        DailyStat::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'date' => now()->toDateString(),
            ],
            [
                'score' => $disciplineScore,
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus_minutes' => $focusMinutes,
                'journaled' => $journalExists,
                'history' => $history->toArray(),
            ]
        );

        $history = DailyStat::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        $mode = auth()->user()->coach_mode;
        $data['mode'] = $mode;

       
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