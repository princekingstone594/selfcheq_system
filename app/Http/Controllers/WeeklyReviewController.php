<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;
use App\Models\DailyStat;
use App\Services\AiCoachService;
use Illuminate\Support\Facades\Cache;

class WeeklyReviewController extends Controller
{
    /**
     * Display the weekly review summary page.
     */
    public function index(AiCoachService $aiCoach): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDate = $today->toDateString();
        $weekStart = $today->copy()->subDays(6)->toDateString();

        // 📊 Last 7 days of DailyStat records
        $stats = DailyStat::where('user_id', $user->id)
            ->where('date', '>=', $weekStart)
            ->orderBy('date')
            ->get();

        // Build arrays for the chart
        $chartLabels = [];
        $scoreChart = [];
        $focusChart = [];
        $taskCompletedChart = [];
        $taskTotalChart = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartLabels[] = $date->format('D M j');

            $stat = $stats->firstWhere('date', $date->toDateString());

            if ($stat) {
                $scoreChart[] = $stat->score ?? 0;
                $focusChart[] = $stat->focus_minutes ?? 0;
                $taskCompletedChart[] = $stat->tasks_completed ?? 0;
                $taskTotalChart[] = $stat->tasks_total ?? 0;
            } else {
                $scoreChart[] = 0;
                $focusChart[] = 0;
                $taskCompletedChart[] = 0;
                $taskTotalChart[] = 0;
            }
        }

        // 😊 Mood trend (from journals)
        $moodLabels = [];
        $moodData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $moodLabels[] = $date->format('D');
            $mood = $user->journals()
                ->whereDate('date', $date->toDateString())
                ->avg('mood');
            $moodData[] = $mood ? round($mood, 1) : null;
        }

        // 📝 Journal entries this week
        $journalEntries = $user->journals()
            ->where('date', '>=', $weekStart)
            ->orderBy('date', 'desc')
            ->get();

        // 🔥 Habit completion summary for the week
        $habits = $user->habits()->where('is_active', true)->get();
        $habitSummaries = [];
        foreach ($habits as $habit) {
            $completedDays = 0;
            $weekData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i)->toDateString();
                $completion = $habit->completions()
                    ->whereDate('date', $date)
                    ->first();
                $isCompleted = $completion && $completion->value >= ($habit->target_value ?? 1);
                $weekData[] = $isCompleted;
                if ($isCompleted) $completedDays++;
            }
            $habitSummaries[] = [
                'habit' => $habit,
                'completed' => $completedDays,
                'total' => 7,
                'weekData' => $weekData,
                'streak' => $habit->currentStreak(),
            ];
        }

        // 🎯 Key metrics
        $avgScore = $stats->isNotEmpty() ? round($stats->avg('score'), 1) : 0;
        $bestDay = $stats->sortByDesc('score')->first();
        $totalTasks = $stats->sum('tasks_completed');
        $totalTasksPossible = $stats->sum('tasks_total');
        $totalFocus = $stats->sum('focus_minutes');
        $totalJournal = $stats->where('journaled', true)->count();
        $taskCompletionRate = $totalTasksPossible > 0
            ? round(($totalTasks / $totalTasksPossible) * 100)
            : 0;

        // 🤖 AI Coach weekly insights
        $history = $stats->map(fn($s) => [
            'date' => Carbon::parse($s->date)->format('M j'),
            'score' => $s->score ?? 0,
            'tasks_completed' => $s->tasks_completed ?? 0,
            'tasks_total' => $s->tasks_total ?? 0,
            'focus' => $s->focus_minutes ?? 0,
            'journaled' => $s->journaled ?? false,
            'mood' => $s->mood ?? null,
        ])->toArray();

        $weeklyInsights = Cache::remember(
            'ai_weekly_insights_' . $user->id . '_' . $todayDate,
            3600,
            function () use ($aiCoach, $history, $user) {
                try {
                    return $aiCoach->generateWeeklyInsights($history, $user->coach_mode ?? 'strict');
                } catch (\Exception $e) {
                    return self::fallbackInsights($history);
                }
            }
        );

        return view('weekly-review.index', [
            'stats' => $stats,
            'chartLabels' => $chartLabels,
            'scoreChart' => $scoreChart,
            'focusChart' => $focusChart,
            'taskCompletedChart' => $taskCompletedChart,
            'taskTotalChart' => $taskTotalChart,
            'moodLabels' => $moodLabels,
            'moodData' => $moodData,
            'journalEntries' => $journalEntries,
            'habitSummaries' => collect($habitSummaries),
            'avgScore' => $avgScore,
            'bestDay' => $bestDay,
            'totalTasks' => $totalTasks,
            'totalTasksPossible' => $totalTasksPossible,
            'totalFocus' => $totalFocus,
            'totalJournal' => $totalJournal,
            'taskCompletionRate' => $taskCompletionRate,
            'weeklyInsights' => $weeklyInsights,
        ]);
    }

    /**
     * Fallback insights when AI is unavailable.
     */
    protected static function fallbackInsights(array $history): string
    {
        $insights = [];

        $avgScore = collect($history)->avg('score');
        $insights[] = "Your average discipline score this week was " . round($avgScore) . "/100.";

        $journaledDays = collect($history)->where('journaled', true)->count();
        $insights[] = "You journaled on {$journaledDays} of 7 days this week.";

        $totalFocus = collect($history)->sum('focus');
        $insights[] = "You accumulated {$totalFocus} minutes of focused work this week.";

        $totalTasks = collect($history)->sum('tasks_completed');
        $totalTasksPossible = collect($history)->sum('tasks_total');
        $insights[] = "You completed {$totalTasks} of {$totalTasksPossible} tasks assigned this week.";

        $bestDay = collect($history)->sortByDesc('score')->first();
        if ($bestDay) {
            $insights[] = "Your best day was {$bestDay['date']} with a score of {$bestDay['score']}/100. Try to replicate what worked!";
        }

        return implode(' ', $insights);
    }
}
