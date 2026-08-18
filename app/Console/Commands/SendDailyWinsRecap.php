<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\DailyWinsRecap;
use Carbon\Carbon;

class SendDailyWinsRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wins:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send end-of-day daily wins recap notifications to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $todayDate = $today->toDateString();

        $users = User::where('notifications_enabled', true)
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            // Gather today's stats
            $tasksToday = $user->tasks()
                ->whereDate('due_date', $todayDate)
                ->get();
            $taskTotal = $tasksToday->count();
            $taskCompleted = $tasksToday->where('is_completed', true)->count();

            $focusMinutes = $user->focusSessions()
                ->whereDate('started_at', $todayDate)
                ->sum('duration');

            $journalExists = $user->journals()
                ->whereDate('date', $todayDate)
                ->exists();

            // Discipline Score (mirrors DashboardController logic)
            $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;

            $habits = $user->habits()
                ->where('is_active', true)
                ->get();

            $habitsCompleted = 0;
            if ($habits->isNotEmpty()) {
                foreach ($habits as $habit) {
                    $completion = $habit->completions()
                        ->whereDate('date', $todayDate)
                        ->first();
                    if ($completion && $completion->value >= ($habit->target_value ?? 1)) {
                        $habitsCompleted++;
                    }
                }
            }
            $habitsTotal = $habits->count();

            // Routines active today
            $routines = $user->routines()
                ->whereDate('date', $todayDate)
                ->get();
            $permanentRoutines = $user->routines()
                ->where('is_permanent', true)
                ->whereNull('reference_id')
                ->get()
                ->filter(fn($r) => $r->isActiveOn($todayDate));

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
            $routineScore = $routineTotal > 0 ? ($routineCompleted / $routineTotal) * 20 : 0;

            $journalScore = $journalExists ? 20 : 0;
            $focusScore = min($focusMinutes / 60, 1) * 20;

            $disciplineScore = round($taskScore + $routineScore + $journalScore + $focusScore);

            // Only send recap if the user had some activity today
            if ($taskTotal === 0 && $focusMinutes === 0 && !$journalExists && $habitsTotal === 0 && $routineTotal === 0) {
                continue;
            }

            $stats = [
                'date' => $today->format('M j, Y'),
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus_minutes' => $focusMinutes,
                'journaled' => $journalExists,
                'habits_completed' => $habitsCompleted,
                'habits_total' => $habitsTotal,
                'discipline_score' => $disciplineScore,
                'streak' => $user->streak ?? 0,
                'xp' => $user->xp ?? 0,
                'level' => $user->level ?? 1,
            ];

            $user->notify(new DailyWinsRecap($stats));
            $sentCount++;
        }

        $this->info("Sent {$sentCount} daily wins recap notification(s).");
    }
}
