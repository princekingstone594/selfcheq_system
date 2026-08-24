<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Examen;
use App\Notifications\DailyNudge;
use App\Services\AiCoachService;
use Carbon\Carbon;

class SendDailyNudges extends Command
{
    /**
     * The 7 declaration chapters available in-app
     * (mirrors BibleChapterController::$allowedReferences).
     */
    protected array $chapters = [
        'Psalms 91', 'Deuteronomy 28', 'Psalms 23',
        'Psalms 27', 'Psalms 121', 'Psalms 118', 'Isaiah 61',
    ];

    protected $signature = 'nudges:send';

    protected $description = 'Send evening AI daily nudges — streak, tomorrow\'s chapter, and one thing to carry';

    public function handle(AiCoachService $ai)
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        // Deterministic rotation: each evening points at a different chapter,
        // cycling through the seven declaration chapters.
        $tomorrowChapter = $this->chapters[$tomorrow->dayOfYear % count($this->chapters)];

        $users = User::where('notifications_enabled', true)
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            // 📊 Today's activity snapshot
            $tasksToday = $user->tasks()->whereDate('due_date', $today->toDateString())->get();
            $taskTotal = $tasksToday->count();
            $taskCompleted = $tasksToday->where('is_completed', true)->count();
            $focusMinutes = (int) $user->focusSessions()
                ->whereDate('started_at', $today->toDateString())
                ->sum('duration');
            $journaled = $user->journals()->whereDate('date', $today->toDateString())->exists();

            // 🌙 Yesterday's Evening Examen reflection (feeds the nudge)
            $examen = Examen::where('user_id', $user->id)
                ->whereDate('date', $yesterday->toDateString())
                ->first();

            // 🔇 Skip users with zero recent engagement — no nagging.
            $recentActivity = $user->dailyStats()
                ->whereDate('date', '>=', $today->copy()->subDays(7)->toDateString())
                ->exists();

            if ($taskTotal === 0 && $focusMinutes === 0 && !$journaled && !$examen && !$recentActivity) {
                continue;
            }

            // 🤖 AI "one thing to carry" — falls back gracefully without an API key
            $nudgeText = $ai->generateDailyNudge([
                'streak' => $user->streak ?? 0,
                'tasks_completed' => $taskCompleted,
                'tasks_total' => $taskTotal,
                'focus_minutes' => $focusMinutes,
                'journaled' => $journaled,
                'examen_reflection' => $examen?->reflection,
                'examen_gratitude' => $examen?->gratitude,
                'tomorrow_chapter' => $tomorrowChapter,
            ]);

            // 📣 Soft share prompt once the streak feels worth celebrating
            $sharePrompt = ($user->streak ?? 0) >= 3 && ($user->streak % 3 === 0);

            $user->notify(new DailyNudge([
                'nudge' => $nudgeText,
                'streak' => $user->streak ?? 0,
                'tomorrow_chapter' => $tomorrowChapter,
                'share_prompt' => $sharePrompt,
            ]));

            $sentCount++;
        }

        $this->info("Sent {$sentCount} daily nudge(s). Tomorrow's chapter: {$tomorrowChapter}");
        return Command::SUCCESS;
    }
}