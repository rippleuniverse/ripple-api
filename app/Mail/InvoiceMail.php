<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        $subject = "Your Invoice - #{$this->invoice->id} ";
        $subjectMap = [
            'pending' => 'is pending payment',
            'in_transit' => 'is in transit',
            'delivered' => 'has been delivered',
            'paid' => 'has been paid',
            'cancelled' => 'has been cancelled'
        ];

        return new Envelope(
            subject: $subject . $subjectMap[$this->invoice->status],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice.invoice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
