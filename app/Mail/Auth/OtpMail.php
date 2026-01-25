<?php

namespace App\Mail\Auth;

use App\Models\OneTimePassword;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OneTimePassword $otp)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->otp->type === 'email_verification' ? 'Verify your email address' : 'Reset your password';
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
