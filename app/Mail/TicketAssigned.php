<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $assignedBy;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $assignedBy)
    {
        $this->ticket = $ticket;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouveau ticket assigné - {$this->ticket->reference}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.assigned',
            with: [
                'ticket' => $this->ticket,
                'assignedBy' => $this->assignedBy,
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
