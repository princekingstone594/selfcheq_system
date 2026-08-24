<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WeeklyRecapReady extends Notification
{
    use Queueable;

    public $stats;

    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->stats;

        return (new MailMessage)
            ->subject('🎉 Your Week in Discipline is ready')
            ->greeting("Hey {$notifiable->name}! 👋")
            ->line('Your week is complete. Here are the highlights:')
            ->line("🔥 Longest streak run: **{$s['longest_run']} day(s)**")
            ->line($s['best_habit']
                ? "⭐ Best habit: **{$s['best_habit']}** ({$s['best_habit_count']}/7 days)"
                : '⭐ Best habit: none yet this week')
            ->line("📖 Chapters declared: **{$s['chapters_declared']}**")
            ->line("🌱 Level {$s['level']} · {$s['growth_title']}")
            ->action('See your full recap', url('/recap'))
            ->line('Rest well this Sunday — a new week starts tomorrow. 🌙');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'weekly_recap_ready',
            'message' => "🎉 Your week in discipline is ready — {$this->stats['longest_run']}-day streak run, {$this->stats['chapters_declared']} chapters declared.",
        ];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $s = $this->stats;

        return new DatabaseMessage([
            'type' => 'weekly_recap_ready',
            'message' => "🎉 Your week is complete: 🔥 {$s['longest_run']}-day run · 📖 {$s['chapters_declared']} chapters · 🌱 {$s['growth_title']}",
            'longest_run' => $s['longest_run'],
            'chapters_declared' => $s['chapters_declared'],
        ]);
    }
}