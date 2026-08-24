<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DailyStat;
use App\Models\DeclarationRead;
use App\Notifications\WeeklyRecapReady;
use Carbon\Carbon;

class SendWeeklyRecap extends Command
{
    /**
     * 🌱 Growth titles by level (mirrors RecapController).
     */
    protected array $growthTitles = [
        1  => 'Seedling',
        3  => 'Sprout',
        5  => 'Sapling',
        10 => 'Evergreen',
        20 => 'Mountain',
        50 => 'Legend',
    ];

    protected $signature = 'recap:send';

    protected $description = 'Send Sunday evening weekly recap notifications';

    public function handle()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->subDays(6)->toDateString();
        $weekEnd = $today->toDateString();

        $users = User::where('notifications_enabled', true)
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            // Active days & longest streak run this week
            $activeDates = DailyStat::where('user_id', $user->id)
                ->whereDate('date', '>=', $weekStart)
                ->whereDate('date', '<=', $weekEnd)
                ->where('score', '>', 0)
                ->orderBy('date')
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d));

            if ($activeDates->isEmpty()) {
                continue; // no activity this week — skip quietly
            }

            $longestRun = 0;
            $currentRun = 0;
            $prev = null;
            foreach ($activeDates as $date) {
                $currentRun = ($prev && $date->diffInDays($prev) === 1) ? $currentRun + 1 : 1;
                $longestRun = max($longestRun, $currentRun);
                $prev = $date;
            }

            // ⭐ Best habit this week
            $bestHabit = null;
            $bestHabitCount = 0;
            foreach ($user->habits()->where('is_active', true)->get() as $habit) {
                $count = $habit->completions()
                    ->whereDate('date', '>=', $weekStart)
                    ->whereDate('date', '<=', $weekEnd)
                    ->get()
                    ->filter(fn($c) => $c->value >= ($habit->target_value ?? 1))
                    ->count();

                if ($count > $bestHabitCount) {
                    $bestHabitCount = $count;
                    $bestHabit = $habit;
                }
            }

            // 📖 Chapters declared this week
            $chaptersDeclared = DeclarationRead::where('user_id', $user->id)
                ->whereDate('date', '>=', $weekStart)
                ->whereDate('date', '<=', $weekEnd)
                ->distinct('reference')
                ->count('reference');

            // 🌱 Growth title
            $level = $user->level ?? 1;
            $growthTitle = 'Seedling';
            foreach ($this->growthTitles as $minLevel => $title) {
                if ($level >= $minLevel) {
                    $growthTitle = $title;
                }
            }

            $user->notify(new WeeklyRecapReady([
                'longest_run' => $longestRun,
                'best_habit' => $bestHabit?->title,
                'best_habit_count' => $bestHabitCount,
                'chapters_declared' => $chaptersDeclared,
                'level' => $level,
                'growth_title' => $growthTitle,
            ]));

            $sentCount++;
        }

        $this->info("Sent {$sentCount} weekly recap notification(s).");
        return Command::SUCCESS;
    }
}