<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Plateforme;
use App\Models\TicketStatut;
use App\Models\TicketPriorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Récupération des données pour les menus déroulants des filtres
        $filterData = [
            'plateformes' => Plateforme::all(),
            'statuts'     => TicketStatut::all(),
            'priorites'   => TicketPriorite::orderBy('niveau', 'desc')->get(),
        ];

        // 2. Construction de la requête avec relations eager loading
        $query = Ticket::with(['statut', 'priorite', 'plateforme', 'module', 'user']);

        // 3. Sécurité par rôle
        if ($user->role->titre === 'Demandeur') {
            $query->where('user_id', $user->id);
        } elseif ($user->role->titre === 'Technicien') {
            $query->where(function($q) use ($user) {
                $q->where('technicien_id', $user->id)
                  ->orWhereNull('technicien_id'); // Voir aussi les tickets en attente d'assignation
            });
        }

        // 4. Application des filtres dynamiques
        $query->when($request->search, function ($q, $search) {
            return $q->where(function($query) use ($search) {
                $query->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%");
            });
        });

        $query->when($request->plateforme, function ($q, $val) {
            return $q->where('plateforme_id', $val);
        });

        $query->when($request->statut, function ($q, $val) {
            return $q->where('statut_id', $val);
        });

        $query->when($request->priorite, function ($q, $val) {
            return $q->where('priorite_id', $val);
        });

        // 5. Pagination avec conservation des paramètres d'URL
        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('support.dashboard', compact('tickets', 'filterData'));
    }
}
