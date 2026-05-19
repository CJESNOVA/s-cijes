<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\CriticalTicketAlert;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // On vérifie si la priorité est critique (ex: ID 4 ou niveau le plus élevé)
        if ($ticket->priorite->niveau >= 4) {
            // On envoie aux superviseurs
            $superviseurs = User::whereHas('role', fn($q) => $q->where('titre', 'Superviseur'))->get();
            
            foreach ($superviseurs as $superviseur) {
                $superviseur->notify(new CriticalTicketAlert($ticket));
            }
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Optionnel : notifier si la priorité change vers critique
        if ($ticket->wasChanged('priorite_id') && $ticket->priorite->niveau >= 4) {
            $superviseurs = User::whereHas('role', fn($q) => $q->where('titre', 'Superviseur'))->get();
            
            foreach ($superviseurs as $superviseur) {
                $superviseur->notify(new CriticalTicketAlert($ticket));
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
