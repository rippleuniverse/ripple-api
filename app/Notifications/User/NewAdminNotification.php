<?php

namespace App\Notifications\User;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdminNotification extends Notification
{
    public function __construct(public User $user, public string $password)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Ripple Team!')
            ->view('emails.user.new-admin', [
                'user' => $this->user,
                'password' => $this->password
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
