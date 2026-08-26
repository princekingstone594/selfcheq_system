<?php

namespace App\Services;

use App\Models\DailyStat;
use App\Models\Devotional;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Gathers every metric, snippet and aggregate the dashboard view needs.
 *
 * Extracted from DashboardController so the data layer is independently
 * testable and the controller stays a thin dispatcher.
 */
class DashboardData
{
    public function __construct(protected AiCoachService $aiCoach)
    {
    }

    public function build(): array
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDate = $today->toDateString();

        // ✅ Tasks (Today)
        $tasksToday = $user->tasks()->whereDate('due_date', $todayDate)->get();
        $taskTotal = $tasksToday->count();
        $taskCompleted = $tasksToday->where('is_completed', true)->count();
        $taskProgress = $taskTotal > 0 ? round(($taskCompleted / $taskTotal) * 100) : 0;

        // 🔁 Routines (materialized + permanent routines active today)
        $routines = $this->todaysRoutines($user, $todayDate);
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        // ⏰ Appointments
        $appointments = $user->appointments()
            ->whereDate('date', $todayDate)
            ->orderBy('time')
            ->get();

        // 🧠 Focus / 📓 Journal / 😊 Mood
        $focusMinutes = (int) $user->focusSessions()
            ->whereDate('started_at', $todayDate)
            ->sum('duration');
        $todayJournal = $user->journals()->whereDate('date', $todayDate)->first();
        $journalExists = $todayJournal !== null;
        $moodAvg = $user->journals()
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->avg('mood');
        $moodAvg = $moodAvg ? round($moodAvg, 1) : null;

        // 📊 Weekly completed tasks
        $weeklyTasks = $user->tasks()
            ->where('due_date', '>=', Carbon::now()->subDays(7))
            ->where('is_completed', true)
            ->count();

        // 📈 Charts (last 7 days) — two grouped queries instead of 14 per-day queries
        [$taskChart, $moodChart, $taskLabels] = $this->weeklyCharts($user);

        // 🎯 Discipline Score
        $disciplineScore = $this->disciplineScore(
            $taskTotal, $taskCompleted, $routineTotal, $routineCompleted,
            $journalExists, $focusMinutes
        );

        return [
            'user' => $user,
            'taskTotal' => $taskTotal,
            'taskCompleted' => $taskCompleted,
            'taskProgress' => $taskProgress,
            'routines' => $routines,
            'routineCompleted' => $routineCompleted,
            'routineTotal' => $routineTotal,
            'appointments' => $appointments,
            'focusMinutes' => $focusMinutes,
            'journalExists' => $journalExists,
            'todayJournal' => $todayJournal,
            'moodAvg' => $moodAvg,
            'weeklyTasks' => $weeklyTasks,
            'taskChart' => $taskChart,
            'taskLabels' => $taskLabels,
            'moodChart' => $moodChart,
            'disciplineScore' => $disciplineScore,
            'nudges' => $this->nudges($taskTotal, $taskCompleted, $journalExists, $focusMinutes),
            'birthdays' => $this->todaysBirthdays($user, $today),
            'devotional' => $this->cachedDevotional($todayDate),
            'coachMessage' => $this->cachedCoachMessage($user, [
                'score' => $disciplineScore,
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus' => $focusMinutes,
                'journal' => $journalExists,
                'mode' => $user->coach_mode,
            ], $todayDate),
            'calendarAppointments' => $this->calendarAppointments($user, $todayDate),
            'calendarTasks' => $this->calendarTasks($user, $todayDate),
            'upcomingAppointments' => $user->appointments()
                ->whereDate('date', '>=', $todayDate)
                ->orderBy('date')->orderBy('time')->take(3)->get(),
            'upcomingTasks' => $user->tasks()
                ->whereDate('due_date', '>=', $todayDate)
                ->orderBy('due_date')->take(3)->get(),
            'history' => DailyStat::where('user_id', $user->id)->latest('date')->take(7)->get(),
            'financials' => $user->financials()->latest()->take(5)->get(),
            'recentNote' => $user->notes()->latest()->first(),
            'tasksToday' => $tasksToday,
        ];
    }
    /**
     * Materialized routines for today merged with permanent routines active today.
     */
    protected function todaysRoutines($user, string $todayDate)
    {
        $routines = $user->routines()->whereDate('date', $todayDate)->get();

        // Exclude financial-linked routines (reference_id) — handled separately.
        // Include morning_devotion linked routines (wake-up time from Devotional).
        $permanentRoutines = $user->routines()
            ->where('is_permanent', true)
            ->where(function ($q) {
                $q->whereNull('reference_id')
                  ->orWhere('reference_type', 'morning_devotion');
            })
            ->get()
            ->filter(fn ($routine) => $routine->isActiveOn($todayDate));

        $existingTitles = $routines->pluck('title')->map(fn ($t) => strtolower(trim($t)))->toArray();
        foreach ($permanentRoutines as $permanent) {
            $key = strtolower(trim($permanent->title));
            if (! in_array($key, $existingTitles)) {
                $routines->push($permanent);
                $existingTitles[] = $key;
            }
        }

        return $routines;
    }

    /**
     * Last-7-days task completion + mood averages in two grouped queries.
     */
    protected function weeklyCharts($user): array
    {
        $start = Carbon::now()->subDays(6)->startOfDay();

        $tasksByDay = $user->tasks()
            ->where('due_date', '>=', $start->toDateString())
            ->where('is_completed', true)
            ->get()
            ->groupBy(fn ($t) => Carbon::parse($t->due_date)->toDateString());

        $moodByDay = $user->journals()
            ->where('date', '>=', $start->toDateString())
            ->get()
            ->groupBy(fn ($j) => Carbon::parse($j->date)->toDateString());

        $taskChart = [];
        $moodChart = [];
        $taskLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $taskLabels[] = Carbon::parse($date)->format('D');
            $taskChart[] = $tasksByDay->get($date, collect())->count();
            $dayMoods = $moodByDay->get($date, collect());
            $moodChart[] = $dayMoods->isNotEmpty() ? $dayMoods->avg('mood') : 0;
        }

        return [$taskChart, $moodChart, $taskLabels];
    }

    protected function disciplineScore(
        int $taskTotal, int $taskCompleted,
        int $routineTotal, int $routineCompleted,
        bool $journalExists, int $focusMinutes
    ): int {
        $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;
        $routineScore = $routineTotal > 0 ? ($routineCompleted / $routineTotal) * 20 : 0;
        $journalScore = $journalExists ? 20 : 0;
        $focusScore = min($focusMinutes / 60, 1) * 20;

        return (int) round($taskScore + $routineScore + $journalScore + $focusScore);
    }

    protected function nudges(int $taskTotal, int $taskCompleted, bool $journalExists, int $focusMinutes): array
    {
        $nudges = [];

        if ($taskTotal > $taskCompleted) {
            $nudges[] = "Finish your remaining tasks 💪";
        }
        if (! $journalExists) {
            $nudges[] = "Write your journal 🧠";
        }
        if ($focusMinutes < 30) {
            $nudges[] = "Do a 30min focus session ⏳";
        }
        if (empty($nudges)) {
            $nudges[] = "You're doing amazing today 🌟";
        }

        return $nudges;
    }

    protected function todaysBirthdays($user, Carbon $today)
    {
        return ($user->contacts ?? collect())->filter(function ($contact) use ($today) {
            return $contact->birthday &&
                Carbon::parse($contact->birthday)->isSameDay($today);
        });
    }

    /**
     * Today's devotional, cached for the rest of the day so the random
     * fallback isn't re-rolled on every dashboard load.
     */
    protected function cachedDevotional(string $todayDate)
    {
        return Cache::remember(
            'dashboard_devotional_' . $todayDate,
            now()->diffInMinutes(now()->endOfDay()),
            fn () => Devotional::whereDate('date', $todayDate)->first()
                ?? Devotional::inRandomOrder()->first()
        );
    }

    protected function cachedCoachMessage($user, array $data, string $todayDate): string
    {
        return Cache::remember(
            'ai_coach_' . $user->id . '_' . $todayDate,
            3600,
            function () use ($data) {
                try {
                    return $this->aiCoach->generate($data);
                } catch (\Exception $e) {
                    return "Stay consistent. Small daily wins build greatness 💪";
                }
            }
        );
    }

    protected function calendarAppointments($user, string $todayDate)
    {
        return $user->appointments()
            ->whereBetween('date', [$todayDate, Carbon::tomorrow()->addDays(6)->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('date');
    }

    protected function calendarTasks($user, string $todayDate)
    {
        return $user->tasks()
            ->whereDate('due_date', '>=', $todayDate)
            ->whereDate('due_date', '<=', Carbon::tomorrow()->addDays(6)->toDateString())
            ->orderBy('due_date')
            ->get()
            ->groupBy('due_date');
    }

    /**
     * Persists today's snapshot into daily_stats. Guarded against schema
     * drift; column introspection is cached to avoid information_schema hits.
     */
    public function persistDailyStat($user, string $todayDate, array $stats): void
    {
        try {
            $statsData = [
                'score' => $stats['score'],
                'tasks_completed' => $stats['tasks_completed'],
                'tasks_total' => $stats['tasks_total'],
                'focus_minutes' => $stats['focus_minutes'],
            ];

            if (self::hasColumn('daily_stats', 'journaled')) {
                $statsData['journaled'] = $stats['journaled'];
            }
            if (self::hasColumn('daily_stats', 'mood')) {
                $statsData['mood'] = $stats['mood'] ?? 0;
            }

            DailyStat::updateOrCreate(
                ['user_id' => $user->id, 'date' => $todayDate],
                $statsData
            );
        } catch (\Throwable $e) {
            // Keep dashboard rendering even if stats persistence is unavailable.
        }
    }

    /** Cached schema introspection — avoids hitting information_schema per load. */
    protected static function hasColumn(string $table, string $column): bool
    {
        return Cache::remember(
            "schema_has_{$table}_{$column}",
            now()->addHour(),
            fn () => Schema::hasColumn($table, $column)
        );
    }
}
