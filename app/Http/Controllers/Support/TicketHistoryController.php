<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketHistorique;
use Illuminate\Http\Request;

class TicketHistoryController extends Controller
{
    /**
     * Afficher l'historique détaillé d'un ticket
     */
    public function show(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions
        $user = auth()->user();
        if ($user->role->titre === 'Demandeur' && $ticket->user_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer tout l'historique avec les relations
        $historiques = TicketHistorique::with('user')
            ->where('ticket_id', $ticket->id)
            ->latest()
            ->paginate(20);

        // Statistiques de l'historique
        $stats = [
            'total_events' => $historiques->total(),
            'status_changes' => TicketHistorique::where('ticket_id', $ticket->id)
                ->where('action', 'like', '%statut%')
                ->count(),
            'priority_changes' => TicketHistorique::where('ticket_id', $ticket->id)
                ->where('action', 'like', '%priorité%')
                ->count(),
            'assignments' => TicketHistorique::where('ticket_id', $ticket->id)
                ->where('action', 'like', '%assign%')
                ->count(),
            'messages' => $ticket->messages()->count(),
        ];

        return view('support.tickets.history', compact('ticket', 'historiques', 'stats'));
    }

    /**
     * API pour récupérer l'historique en temps réel
     */
    public function api(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions
        $user = auth()->user();
        if ($user->role->titre === 'Demandeur' && $ticket->user_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $historiques = TicketHistorique::with('user')
            ->where('ticket_id', $ticket->id)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'historiques' => $historiques,
            'total' => $historiques->count(),
        ]);
    }

    /**
     * Créer une entrée d'historique (utilisé par les autres contrôleurs)
     */
    public static function createEntry(Ticket $ticket, $action, $details = null)
    {
        return TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'details' => $details,
        ]);
    }
}
