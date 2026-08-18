<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DailyWinsRecap extends Notification
{
    use Queueable;

    public $stats;

    /**
     * Create a new notification instance.
     */
    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $stats = $this->stats;

        return (new MailMessage)
            ->subject('🎉 Your Daily Wins Recap — ' . $stats['date'])
            ->greeting("Hey {$notifiable->name}! 👋")
            ->line("Here's your end-of-day recap for {$stats['date']}:")
            ->line("• **Tasks**: {$stats['tasks_completed']}/{$stats['tasks_total']} completed")
            ->line($stats['focus_minutes'] > 0
                ? "• **Focus**: {$stats['focus_minutes']} min focused ⏳"
                : '• **Focus**: No focus session today ⏳')
            ->line($stats['journaled']
                ? '• **Journal**: Written ✍️'
                : '• **Journal**: Not written today 📝')
            ->line($stats['habits_completed'] > 0
                ? "• **Habits**: {$stats['habits_completed']}/{$stats['habits_total']} completed"
                : '• **Habits**: No habits completed today')
            ->line("• **Discipline Score**: {$stats['discipline_score']}/100")
            ->line("• **Streak**: {$stats['streak']} days 🔥")
            ->line("• **Total XP**: {$stats['xp']} (Level {$stats['level']})")
            ->action('View Dashboard', url('/dashboard'))
            ->line('Keep building your discipline — every day counts! 💪');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $stats = $this->stats;

        return [
            'type' => 'daily_wins_recap',
            'date' => $stats['date'],
            'tasks_completed' => $stats['tasks_completed'],
            'tasks_total' => $stats['tasks_total'],
            'focus_minutes' => $stats['focus_minutes'],
            'journaled' => $stats['journaled'],
            'habits_completed' => $stats['habits_completed'],
            'habits_total' => $stats['habits_total'],
            'discipline_score' => $stats['discipline_score'],
            'streak' => $stats['streak'],
            'xp' => $stats['xp'],
            'level' => $stats['level'],
            'message' => "Your daily recap: {$stats['tasks_completed']}/{$stats['tasks_total']} tasks, {$stats['focus_minutes']} min focus, score {$stats['discipline_score']}/100 🔥",
        ];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $stats = $this->stats;

        return new DatabaseMessage([
            'type' => 'daily_wins_recap',
            'date' => $stats['date'],
            'message' => "🎉 Daily Wins: {$stats['tasks_completed']}/{$stats['tasks_total']} tasks, {$stats['focus_minutes']}min focus, score {$stats['discipline_score']}/100",
            'discipline_score' => $stats['discipline_score'],
            'tasks_completed' => $stats['tasks_completed'],
            'tasks_total' => $stats['tasks_total'],
            'focus_minutes' => $stats['focus_minutes'],
            'journaled' => $stats['journaled'],
            'habits_completed' => $stats['habits_completed'],
            'streak' => $stats['streak'],
        ]);
    }
}
