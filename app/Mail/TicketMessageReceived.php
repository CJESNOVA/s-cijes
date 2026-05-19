<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $message;
    public $messageAuthor;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, TicketMessage $ticketMessage)
    {
        $this->ticket = $ticket;
        $this->message = $ticketMessage;
        $this->messageAuthor = $ticketMessage->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle réponse - Ticket {$this->ticket->reference}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.message_received',
            with: [
                'ticket' => $this->ticket,
                'message' => $this->message,
                'messageAuthor' => $this->messageAuthor,
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
