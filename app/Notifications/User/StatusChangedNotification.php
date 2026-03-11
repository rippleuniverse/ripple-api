<?php

namespace App\Notifications\User;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification
{
    public function __construct(public User $user, public ?string $reason = null)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Status Changed to: ' . $this->user->status)
            ->view('emails.user.' . $this->user->status, [
                'user' => $this->user,
                'reason' => $this->reason
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
