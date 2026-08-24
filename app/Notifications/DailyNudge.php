<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DailyNudge extends Notification
{
    use Queueable;

    public $payload;

    /**
     * Create a new notification instance.
     *
     * Expected $payload keys:
     *  - nudge            (string) the AI "one thing to carry" line
     *  - streak           (int)    current streak
     *  - tomorrow_chapter (string) tomorrow's declaration chapter
     *  - share_prompt     (bool)   whether to include a soft share prompt
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
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
        $p = $this->payload;
        $streak = $p['streak'];
        $chapter = $p['tomorrow_chapter'];

        $mail = (new MailMessage)
            ->subject('🌙 Your evening nudge — tomorrow starts tonight')
            ->greeting("Hey {$notifiable->name}! 👋")
            ->line($p['nudge'])
            ->line("📖 Tomorrow's declaration chapter: **{$chapter}**");

        if ($streak > 0) {
            $mail->line("🔥 Current streak: **{$streak} day" . ($streak === 1 ? '' : 's') . "**");
        }

        if (!empty($p['share_prompt'])) {
            $mail->line("You've been consistent lately — want to share your {$streak}-day streak with someone who'd cheer you on?");
        }

        return $mail
            ->action('Command Your Morning', url('/devotional'))
            ->line('Rest well. Tomorrow is already set up for you. 🌙');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'daily_nudge',
            'nudge' => $this->payload['nudge'],
            'streak' => $this->payload['streak'],
            'tomorrow_chapter' => $this->payload['tomorrow_chapter'],
            'share_prompt' => $this->payload['share_prompt'],
            'message' => $this->payload['nudge'],
        ];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $p = $this->payload;
        $streak = $p['streak'];

        $message = $p['nudge'] . " Tomorrow: {$p['tomorrow_chapter']} 📖";

        if ($streak > 0) {
            $message = "🔥 {$streak}-day streak · " . $message;
        }

        return new DatabaseMessage([
            'type' => 'daily_nudge',
            'nudge' => $p['nudge'],
            'streak' => $streak,
            'tomorrow_chapter' => $p['tomorrow_chapter'],
            'share_prompt' => $p['share_prompt'],
            'message' => $message,
        ]);
    }
}