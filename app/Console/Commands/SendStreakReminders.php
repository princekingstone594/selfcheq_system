<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\StreakReminder;
use Carbon\Carbon;

class SendStreakReminders extends Command
{
    protected $signature = 'streak:remind';

    protected $description = 'Evening streak guard — warn users with a 3+ day streak who have no activity today';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Users with something to lose (streak >= 3), notifications on,
        // who haven't recorded any activity today.
        $users = User::where('notifications_enabled', true)
            ->whereNotNull('email')
            ->where('streak', '>=', 3)
            ->where(function ($q) use ($today) {
                $q->whereNull('last_completed_date')
                  ->orWhere('last_completed_date', '!=', $today);
            })
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            // Double-check: maybe they completed tasks/focused/journaled today
            // but last_completed_date wasn't refreshed by another flow.
            $hasActivityToday = $user->tasks()
                ->whereDate('due_date', $today)
                ->where('is_completed', true)
                ->exists()
                || $user->focusSessions()->whereDate('started_at', $today)->exists()
                || $user->journals()->whereDate('date', $today)->exists();

            if ($hasActivityToday) {
                continue;
            }

            $user->notify(new StreakReminder([
                'streak' => $user->streak,
            ]));

            $sentCount++;
        }

        $this->info("Sent {$sentCount} streak reminder(s).");
        return Command::SUCCESS;
    }
}
