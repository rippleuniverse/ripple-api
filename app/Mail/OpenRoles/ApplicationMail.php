<?php

namespace App\Mail\OpenRoles;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public JobApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Application Received for {$this->application->role->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.open-roles.application',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
