<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketStatut;
use App\Models\TicketPriorite;
use App\Models\Plateforme;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Dashboard d'assignation pour les techniciens
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Tickets non assignés
        $unassignedTickets = Ticket::whereNull('technicien_id')
            ->with(['user', 'plateforme', 'priorite'])
            ->latest()
            ->get();
            
        // Tickets assignés au technicien
        $myTickets = Ticket::where('technicien_id', $user->id)
            ->with(['user', 'plateforme', 'priorite', 'statut'])
            ->latest()
            ->get();
            
        // Statistiques d'assignation
        $stats = [
            'unassigned_count' => $unassignedTickets->count(),
            'my_tickets_count' => $myTickets->count(),
            'urgent_unassigned' => $unassignedTickets->where('priorite.niveau', '>=', 3)->count(),
            'in_progress' => $myTickets->where('statut.nom', 'En cours')->count(),
        ];
        
        // Techniciens disponibles
        $availableTechnicians = User::whereHas('role', function($query) {
            $query->whereIn('titre', ['Technicien', 'Administrateur']);
        })
        ->where('etat', true)
        ->get();
        
        return view('support.assignment.dashboard', compact(
            'unassignedTickets',
            'myTickets',
            'stats',
            'availableTechnicians'
        ));
    }
    
    /**
     * Liste de tous les tickets (assignés et non assignés)
     */
    public function unassigned(Request $request)
    {
        $query = Ticket::with(['user', 'plateforme', 'priorite', 'categorie', 'technicien', 'statut']);
            
        // Filtres
        if ($request->priorite) {
            $query->where('priorite_id', $request->priorite);
        }
        
        if ($request->plateforme) {
            $query->where('plateforme_id', $request->plateforme);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->search . '%')
                  ->orWhere('titre', 'like', '%' . $request->search . '%');
            });
        }
        
        $tickets = $query->latest()->paginate(20)->withQueryString();
        
        $priorites = TicketPriorite::all();
        $plateformes = Plateforme::where('etat', true)->get();
        $technicians = User::where('etat', true)
            ->whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Administrateur']);
            })
            ->with('role')
            ->get();
        
        return view('support.assignment.unassigned', compact(
            'tickets',
            'priorites',
            'plateformes',
            'technicians'
        ));
    }
    
    /**
     * Assigner un ticket à un technicien
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'technicien_id' => 'required|exists:users,id',
            'note_assignation' => 'nullable|string|max:500',
        ]);
        
        // Vérifier que le ticket n'est pas déjà assigné
        if ($ticket->technicien_id) {
            return back()->with('error', 'Ce ticket est déjà assigné.');
        }
        
        $technicien = User::findOrFail($request->technicien_id);
        
        // Vérifier que le technicien a le bon rôle
        if (!in_array($technicien->role->titre, ['Technicien', 'Administrateur'])) {
            return back()->with('error', 'Cet utilisateur n\'est pas un technicien.');
        }
        
        // Assigner le ticket
        $ticket->update([
            'technicien_id' => $technicien->id,
            'statut_id' => 3, // "En cours"
        ]);
        
        // Ajouter à l'historique
        $ticket->historiques()->create([
            'user_id' => Auth::id(),
            'action' => 'Assigné à ' . $technicien->nom . ' ' . $technicien->prenom,
            'details' => $request->note_assignation,
        ]);
        
        // Créer la notification pour le technicien
        Notification::create([
            'user_id' => $technicien->id,
            'type' => 'ticket_assigned',
            'title' => 'Nouveau ticket assigné',
            'message' => 'Le ticket ' . $ticket->reference . ' vous a été assigné.',
            'notifiable_type' => Ticket::class,
            'notifiable_id' => $ticket->id,
            'icon' => 'user-plus',
            'color' => 'blue',
        ]);
        
        return back()->with('success', 'Ticket assigné avec succès à ' . $technicien->nom . ' ' . $technicien->prenom);
    }
    
    /**
     * Réassigner un ticket
     */
    public function reassign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'technicien_id' => 'required|exists:users,id',
            'motif_reassignation' => 'required|string|max:500',
        ]);
        
        $oldTechnicien = $ticket->technicien;
        $newTechnicien = User::findOrFail($request->technicien_id);
        
        // Vérifier que le nouveau technicien a le bon rôle
        if (!in_array($newTechnicien->role->titre, ['Technicien', 'Administrateur'])) {
            return back()->with('error', 'Cet utilisateur n\'est pas un technicien.');
        }
        
        // Réassigner le ticket
        $ticket->update([
            'technicien_id' => $newTechnicien->id,
        ]);
        
        // Ajouter à l'historique
        $ticket->historiques()->create([
            'user_id' => Auth::id(),
            'action' => 'Réassigné de ' . $oldTechnicien->nom . ' à ' . $newTechnicien->nom,
            'details' => $request->motif_reassignation,
        ]);
        
        // Notification pour l'ancien technicien
        if ($oldTechnicien) {
            Notification::create([
                'user_id' => $oldTechnicien->id,
                'type' => 'ticket_reassigned',
                'title' => 'Ticket réassigné',
                'message' => 'Le ticket ' . $ticket->reference . ' a été réassigné.',
                'notifiable_type' => Ticket::class,
                'notifiable_id' => $ticket->id,
                'icon' => 'activity',
                'color' => 'orange',
            ]);
        }
        
        // Notification pour le nouveau technicien
        Notification::create([
            'user_id' => $newTechnicien->id,
            'type' => 'ticket_assigned',
            'title' => 'Ticket réassigné',
            'message' => 'Le ticket ' . $ticket->reference . ' vous a été réassigné.',
            'notifiable_type' => Ticket::class,
            'notifiable_id' => $ticket->id,
            'icon' => 'user-plus',
            'color' => 'blue',
        ]);
        
        return back()->with('success', 'Ticket réassigné avec succès');
    }
    
    /**
     * Assignation automatique
     */
    public function autoAssign(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'strategy' => 'required|in:round_robin,least_loaded,priority_based',
        ]);
        
        $tickets = Ticket::whereIn('id', $request->ticket_ids)
            ->whereNull('technicien_id')
            ->get();
            
        if ($tickets->isEmpty()) {
            return back()->with('error', 'Aucun ticket non assigné trouvé.');
        }
        
        $techniciens = User::whereHas('role', function($query) {
            $query->whereIn('titre', ['Technicien', 'Administrateur']);
        })
        ->where('etat', true)
        ->get();
        
        if ($techniciens->isEmpty()) {
            return back()->with('error', 'Aucun technicien disponible.');
        }
        
        $assignedCount = 0;
        
        foreach ($tickets as $ticket) {
            $technicien = $this->selectTechnician($techniciens, $ticket, $request->strategy);
            
            if ($technicien) {
                $ticket->update([
                    'technicien_id' => $technicien->id,
                    'statut_id' => 3,
                ]);
                
                $ticket->historiques()->create([
                    'user_id' => Auth::id(),
                    'action' => 'Assigné automatiquement à ' . $technicien->nom,
                    'details' => 'Stratégie: ' . $request->strategy,
                ]);
                
                Notification::create([
                    'user_id' => $technicien->id,
                    'type' => 'ticket_assigned',
                    'title' => 'Ticket assigné automatiquement',
                    'message' => 'Le ticket ' . $ticket->reference . ' vous a été assigné automatiquement.',
                    'notifiable_type' => Ticket::class,
                    'notifiable_id' => $ticket->id,
                    'icon' => 'user-plus',
                    'color' => 'blue',
                ]);
                
                $assignedCount++;
            }
        }
        
        return back()->with('success', $assignedCount . ' ticket(s) assigné(s) automatiquement.');
    }
    
    /**
     * Sélectionner un technicien selon la stratégie
     */
    private function selectTechnician($techniciens, $ticket, $strategy)
    {
        switch ($strategy) {
            case 'round_robin':
                // Rotation simple
                static $index = 0;
                $technicien = $techniciens[$index % $techniciens->count()];
                $index++;
                break;
                
            case 'least_loaded':
                // Technicien avec le moins de tickets actifs
                $technicien = $techniciens->sortBy(function($tech) {
                    return Ticket::where('technicien_id', $tech->id)
                        ->whereIn('statut_id', [1, 3]) // Ouvert ou En cours
                        ->count();
                })->first();
                break;
                
            case 'priority_based':
                // Pour les tickets urgents, technicien le plus expérimenté
                if ($ticket->priorite->niveau >= 3) {
                    $technicien = $techniciens->sortByDesc('created_at')->first();
                } else {
                    $technicien = $techniciens->random();
                }
                break;
                
            default:
                $technicien = $techniciens->random();
        }
        
        return $technicien;
    }
    
    /**
     * Statistiques d'assignation
     */
    public function stats()
    {
        $stats = [
            'total_tickets' => Ticket::count(),
            'unassigned' => Ticket::whereNull('technicien_id')->count(),
            'assigned' => Ticket::whereNotNull('technicien_id')->count(),
            
            'by_priority' => Ticket::with(['priorite'])
                ->whereNull('technicien_id')
                ->get()
                ->groupBy('priorite.nom')
                ->map->count(),
                
            'by_platform' => Ticket::with(['plateforme'])
                ->whereNull('technicien_id')
                ->get()
                ->groupBy('plateforme.nom')
                ->map->count(),
                
            'technician_load' => User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Administrateur']);
            })
            ->withCount(['tickets' => function($query) {
                $query->whereIn('statut_id', [1, 3]);
            }])
            ->get()
            ->sortByDesc('tickets_count'),
        ];
        
        return view('support.assignment.stats', compact('stats'));
    }
}
