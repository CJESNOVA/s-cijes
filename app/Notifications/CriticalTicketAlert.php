<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class CriticalTicketAlert extends Notification
{
    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $url = route('tickets.show', $this->ticket);

        return (new SlackMessage)
            ->error() // Couleur rouge sur le côté
            ->content('**ALERTE : Nouveau Ticket Critique**')
            ->attachment(function ($attachment) use ($url) {
                $attachment->title($this->ticket->reference . ' : ' . $this->ticket->titre, $url)
                           ->fields([
                               'Plateforme' => $this->ticket->plateforme->nom,
                               'Demandeur'  => $this->ticket->user->prenom . ' ' . $this->ticket->user->nom,
                               'Priorité'   => 'CRITIQUE',
                           ]);
            });
    }
}
