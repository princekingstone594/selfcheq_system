<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BirthdayReminder extends Notification
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
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
            ->subject("🎂 Birthday Reminder: {$this->contact->name}")
            ->line("Today is <strong>{$this->contact->name}</strong>'s birthday!")
            ->line($this->contact->relationship
                ? "Relationship: {$this->contact->relationship}"
                : '')
            ->line($this->contact->birthday
                ? "Born: " . \Carbon\Carbon::parse($this->contact->birthday)->format('M j, Y')
                : '')
            ->action('View Contacts', url('/contacts'))
            ->line('Wishing them a wonderful day! 🎉');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_id' => $this->contact->id,
            'name' => $this->contact->name,
            'relationship' => $this->contact->relationship,
            'birthday' => $this->contact->birthday,
            'type' => 'birthday_reminder',
        ];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'contact_id' => $this->contact->id,
            'name' => $this->contact->name,
            'message' => "Today is {$this->contact->name}'s birthday!",
            'type' => 'birthday_reminder',
        ]);
    }
}
