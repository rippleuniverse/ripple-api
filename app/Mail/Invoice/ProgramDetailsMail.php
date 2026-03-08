<?php

namespace App\Mail\Invoice;

use App\Models\Invoice;
use App\Models\Program;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProgramDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Program $program, public Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Program Details - {$this->program->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice.program-details',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('private', $this->program->file),
        ];
    }
}
