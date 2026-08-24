<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\DailyStat;
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

        // 🔥 Discipline heatmap — last ~13 weeks of daily scores (GitHub-style grid)
        $heatmapStart = $today->copy()->subDays(90);
        $statScores = DailyStat::where('user_id', $user->id)
            ->whereDate('date', '>=', $heatmapStart->toDateString())
            ->pluck('score', 'date');

        // Grid starts on the Sunday on/before the window start so columns align to weeks
        $gridStart = $heatmapStart->copy()->startOfWeek(Carbon::SUNDAY);
        $heatmap = [];
        for ($d = $gridStart->copy(); $d->lte($today); $d->addDay()) {
            $ds = $d->toDateString();
            $heatmap[] = [
                'date' => $ds,
                'day' => $d->format('M j'),
                'score' => $statScores[$ds] ?? null,
                'is_today' => $ds === $todayDate,
            ];
        }

        // 📈 Weekly trend — discipline score over the last 7 days
        $trendScores = [];
        for ($i = 6; $i >= 0; $i--) {
            $ds = Carbon::now()->subDays($i)->toDateString();
            $trendScores[] = $statScores[$ds] ?? 0;
        }

        // 🧑‍🚀 Reactive avatar state
        //   glowing  → streak intact (activity today or yesterday)
        //   tired    → streak broken (last activity older than yesterday)
        //   sleepy   → early morning before any activity today
        $lastActive = $user->last_completed_date ? Carbon::parse($user->last_completed_date) : null;
        $doneToday = $lastActive && $lastActive->isToday();
        $doneYesterday = $lastActive && $lastActive->isYesterday();

        if ($doneToday || $doneYesterday) {
            $avatarState = 'glowing';
        } elseif ($lastActive && $user->streak > 0) {
            $avatarState = 'tired';
        } elseif ($today->hour < 11) {
            $avatarState = 'sleepy';
        } else {
            $avatarState = 'idle';
        }

        return view('progress.index', compact(
            'disciplineScore',
            'focusMinutes',
            'moodAvg',
            'taskChart',
            'taskLabels',
            'moodChart',
            'nudges',
            'birthdays',
            'taskTotal',
            'heatmap',
            'trendScores',
            'avatarState'
        ));
    }
}