<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineReminder extends Notification
{
    use Queueable;

    public $task;
    /**
     * Create a new notification instance.
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('Task Deadline Reminder')
            ->line("Your task **{$this->task->title}** is due today.")
            ->line($this->task->deadline
                ? "Due: " . \Carbon\Carbon::parse($this->task->deadline)->format('M j, Y g:ia')
                : 'Due today')
            ->line($this->task->description
                ? "Details: " . \Str::limit($this->task->description, 100)
                : '')
            ->action('View Task', url('/tasks'))
            ->line('Stay focused and get it done! 💪');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'deadline' => $this->task->deadline,
            'description' => $this->task->description,
            'type' => 'task_deadline',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => "The task '{$this->task->title}' is due today!",
            'type' => 'task_deadline',
        ];
    }
}
