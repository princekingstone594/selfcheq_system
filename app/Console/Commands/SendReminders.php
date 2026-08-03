<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\BirthdayReminder;
use App\Notifications\TaskDeadlineReminder;
use Carbon\Carbon;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send birthday and deadline reminders';

    public function handle()
    {
        $today = Carbon::today();

        foreach (User::all() as $user) {

            // 🎂 Birthdays
            foreach ($user->contacts as $contact) {
                if ($contact->birthday &&
                    Carbon::parse($contact->birthday)->format('m-d') === $today->format('m-d')) {

                    $user->notify(new BirthdayReminder($contact));
                }
            }

            // ⏰ Task deadlines
            foreach ($user->tasks as $task) {
                if ($task->deadline &&
                    Carbon::parse($task->deadline)->isToday() &&
                    !$task->is_completed) {

                    $user->notify(new TaskDeadlineReminder($task));
                }
            }
        }

        return Command::SUCCESS;
    }
}