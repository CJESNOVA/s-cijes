<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Plateforme;
use App\Models\TicketStatut;
use App\Models\TicketPriorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTicketsController extends Controller
{
    /**
     * Affiche les tickets de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Récupération des données pour les filtres
        $filterData = [
            'plateformes' => Plateforme::all(),
            'statuts'     => TicketStatut::all(),
            'priorites'   => TicketPriorite::orderBy('niveau', 'desc')->get(),
        ];

        // 2. Construction de la requête pour les tickets de l'utilisateur
        $query = Ticket::with(['statut', 'priorite', 'plateforme', 'module', 'technicien'])
            ->where('user_id', $user->id);

        // 3. Application des filtres dynamiques
        $query->when($request->search, function ($q, $search) {
            return $q->where(function($query) use ($search) {
                $query->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%");
            });
        });

        $query->when($request->plateforme_id, function ($q, $val) {
            return $q->where('plateforme_id', $val);
        });

        $query->when($request->statut_id, function ($q, $val) {
            return $q->where('statut_id', $val);
        });

        $query->when($request->priorite_id, function ($q, $val) {
            return $q->where('priorite_id', $val);
        });

        // 4. Pagination avec conservation des paramètres d'URL
        $tickets = $query->latest()->paginate(10)->withQueryString();

        // 5. Statistiques pour l'utilisateur
        $stats = [
            'total' => Ticket::where('user_id', $user->id)->count(),
            'ouverts' => Ticket::where('user_id', $user->id)
                ->where('statut_id', 1)->count(), // Ouvert
            'en_cours' => Ticket::where('user_id', $user->id)
                ->where('statut_id', 3)->count(), // En cours
            'resolus' => Ticket::where('user_id', $user->id)
                ->where('statut_id', 4)->count(), // Résolu
            'fermes' => Ticket::where('user_id', $user->id)
                ->where('statut_id', 5)->count(), // Fermé
        ];

        return view('support.my-tickets.index', compact('tickets', 'filterData', 'stats'));
    }
}
