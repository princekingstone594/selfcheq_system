<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'reminders:tasks';
    protected $description = 'Send task reminders';

    public function handle()
    {
        $now = Carbon::now()->format('H:i');

        $tasks = Task::where('reminder_time', $now)
            ->where('is_completed', false)
            ->get();

        foreach ($tasks as $task) {
            \Log::info("Reminder: {$task->title}");
        }

        return 0;
    }
}