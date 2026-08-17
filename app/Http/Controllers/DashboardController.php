<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AiCoachService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\DailyStat;
use App\Models\Devotional;
use App\Models\Appointment;
use App\Models\Task;
use App\Models\Note;

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

        // 🔁 Routines (materialized + permanent routines active today)
        $routines = $user->routines()->whereDate('date', $todayDate)->get();

        // 🔒 Merge in permanent routines active on this day
        $permanentRoutines = $user->routines()
            ->where('is_permanent', true)
            ->get()
            ->filter(function ($routine) use ($todayDate) {
                return $routine->isActiveOn($todayDate);
            });

        $existingTitles = $routines->pluck('title')->map(fn($t) => strtolower(trim($t)))->toArray();
        foreach ($permanentRoutines as $permanent) {
            $key = strtolower(trim($permanent->title));
            if (!in_array($key, $existingTitles)) {
                $routines->push($permanent);
                $existingTitles[] = $key;
            }
        }

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

        // 📓 Journal (today's entry — used for dashboard snip)
        $todayJournal = $user->journals()
            ->whereDate('date', $todayDate)
            ->first();
        $journalExists = $todayJournal !== null;

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

        //  Devotional (for dashboard snippet)
        $devotional = Devotional::whereDate('date', $todayDate)->first();
        if (!$devotional) {
            $devotional = Devotional::inRandomOrder()->first();
        }

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

        // 📅 Calendar preview (next 7 days)
        $calendarStart = $today->copy();
        $calendarEnd = $today->copy()->addDays(7);
        $calendarAppointments = $user->appointments()
            ->whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('date');
        $calendarTasks = $user->tasks()
            ->whereDate('due_date', '>=', $todayDate)
            ->whereDate('due_date', '<=', $calendarEnd->toDateString())
            ->orderBy('due_date')
            ->get()
            ->groupBy('due_date');

        // 🗓️ Upcoming items for dashboard calendar snippet (next 3 upcoming)
        $upcomingAppointments = $user->appointments()
            ->whereDate('date', '>=', $todayDate)
            ->orderBy('date')
            ->orderBy('time')
            ->take(3)
            ->get();

        $upcomingTasks = $user->tasks()
            ->whereDate('due_date', '>=', $todayDate)
            ->orderBy('due_date')
            ->take(3)
            ->get();

        // 📊 History (last 7)
        $history = DailyStat::where('user_id', $user->id)
            ->latest('date')
            ->take(7)
            ->get();

        // 💾 Save today's stats (guarded so dashboard still renders if schema is incomplete)
        try {
            $statsData = [
                'score' => $disciplineScore,
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus_minutes' => $focusMinutes,
            ];

            if (Schema::hasColumn('daily_stats', 'journaled')) {
                $statsData['journaled'] = $journalExists;
            }

            if (Schema::hasColumn('daily_stats', 'mood')) {
                $statsData['mood'] = $moodAvg ?? 0;
            }

            DailyStat::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $todayDate,
                ],
                $statsData
            );
        } catch (\Throwable $e) {
            // Keep dashboard rendering even if stats persistence is unavailable
        }

        $financials = $user->financials()->latest()->take(5)->get();

        $recentNote = $user->notes()->latest()->first();

        return view('dashboard', compact(
            'taskTotal',
            'taskCompleted',
            'taskProgress',
            'routineCompleted',
            'routineTotal',
            'appointments',
            'focusMinutes',
            'journalExists',
            'todayJournal',
            'moodAvg',
            'weeklyTasks',
            'taskChart',
            'taskLabels',
            'moodChart',
            'disciplineScore',
            'nudges',
            'birthdays',
            'coachMessage',
            'tasksToday',
            'routines',
            'devotional',
            'calendarAppointments',
            'calendarTasks',
            'upcomingAppointments',
            'upcomingTasks',
            'financials',
            'recentNote'
        ));
    }
}