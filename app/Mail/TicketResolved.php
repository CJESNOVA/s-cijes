<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketResolved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $resolvedBy;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $resolvedBy)
    {
        $this->ticket = $ticket;
        $this->resolvedBy = $resolvedBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ticket {$this->ticket->reference} - Résolu",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.resolved',
            with: [
                'ticket' => $this->ticket,
                'resolvedBy' => $this->resolvedBy,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
