<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class CriticalTicketReminder extends Notification
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
        return (new SlackMessage)
            ->warning() // Couleur orange pour indiquer un retard
            ->content("**RAPPEL : Ticket toujours sans réponse après 2h**")
            ->attachment(function ($attachment) {
                $attachment->title($this->ticket->reference . ' : ' . $this->ticket->titre, route('tickets.show', $this->ticket))
                           ->fields([
                               'Temps écoulé' => $this->ticket->created_at->diffForHumans(),
                               'Plateforme' => $this->ticket->plateforme->nom,
                               'Demandeur' => $this->ticket->user->prenom . ' ' . $this->ticket->user->nom,
                               'Priorité' => 'CRITIQUE',
                           ]);
            });
    }
}
