<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketStatut;
use Illuminate\Http\Request;

class TicketStatusController extends Controller
{
    /**
     * Met à jour le statut du ticket.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'statut_id' => 'required|exists:ticket_statuts,id',
        ]);

        $nouveauStatut = TicketStatut::find($validated['statut_id']);
        
        // Mise à jour du ticket
        $ticket->update([
            'statut_id' => $nouveauStatut->id,
            // Si le ticket est marqué comme résolu, on enregistre la date
            'date_fermeture' => $nouveauStatut->code === 'resolu' ? now() : $ticket->date_fermeture,
        ]);

        return back()->with('success', "Le statut du ticket a été mis à jour : {$nouveauStatut->nom}");
    }
}
