<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class StreakReminder extends Notification
{
    use Queueable;

    public $payload;

    /**
     * @param array $payload  keys: streak (int)
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $streak = $this->payload['streak'];

        return (new MailMessage)
            ->subject("🔥 Your {$streak}-day streak needs you today")
            ->greeting("Hey {$notifiable->name}!")
            ->line("You've built a **{$streak}-day streak** — but there's no activity logged for today yet.")
            ->line("One task, one journal entry or one focus session keeps the fire alive. Don't let today be the day it resets.")
            ->action('Keep the Streak Alive', url('/dashboard'))
            ->line('Small wins daily. That\'s the whole game. 💪');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'streak_reminder',
            'streak' => $this->payload['streak'],
            'message' => "🔥 Your {$this->payload['streak']}-day streak needs you today — log some activity before midnight!",
        ];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'streak_reminder',
            'streak' => $this->payload['streak'],
            'message' => "🔥 Your {$this->payload['streak']}-day streak needs you today — log some activity before midnight!",
        ]);
    }
}
