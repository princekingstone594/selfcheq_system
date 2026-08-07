<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Routine;
use App\Models\User;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'reminders:tasks';
    protected $description = 'Send task and routine alarm notifications';

    public function handle()
    {
        $now = Carbon::now()->format('H:i');
        $today = Carbon::now()->toDateString();

        foreach (User::all() as $user) {
            // Skip if user has disabled reminders
            if (!$user->reminders_enabled) {
                continue;
            }

            // 🔔 Tasks with alarm enabled — check both alarm_time and reminder_time
            $tasks = Task::where('user_id', $user->id)
                ->where('alarm_enabled', true)
                ->where(function ($q) use ($now) {
                    $q->where('alarm_time', $now)
                      ->orWhere('reminder_time', $now);
                })
                ->where('is_completed', false)
                ->get();

            foreach ($tasks as $task) {
                \Log::info("⏰ Task alarm: {$task->title} for user {$user->name}");
            }

            // ⏰ Routines (every routine has an alarm — discipline)
            $routines = Routine::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->where('is_completed', false)
                ->get();

            foreach ($routines as $routine) {
                if ($routine->reminder_time &&
                    Carbon::parse($routine->reminder_time)->format('H:i') === $now) {
                    \Log::info("⏰ Routine alarm: {$routine->title} for user {$user->name}");
                }
            }
        }

        return 0;
    }
}
