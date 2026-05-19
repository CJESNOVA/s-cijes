<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\TicketSatisfaction;

class TicketSatisfactionReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $satisfaction;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, TicketSatisfaction $satisfaction)
    {
        $this->ticket = $ticket;
        $this->satisfaction = $satisfaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Satisfaction client - {$this->ticket->reference}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.satisfaction_received',
            with: [
                'ticket' => $this->ticket,
                'satisfaction' => $this->satisfaction,
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
