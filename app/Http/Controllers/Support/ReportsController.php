<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketStatut;
use App\Models\TicketPriorite;
use App\Models\Plateforme;
use App\Models\Categorie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;
use App\Exports\TechniciensExport;
use App\Exports\PerformanceExport;

class ReportsController extends Controller
{
    /**
     * Page des rapports pour superviseurs
     */
    public function index(Request $request)
    {
        // Vérifier que l'utilisateur est superviseur ou administrateur
        if (!in_array(Auth::user()->role->titre, ['Superviseur', 'Administrateur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Statistiques générales
        $stats = [
            'total_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
            'tickets_resolus' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('statut', function($query) {
                    $query->where('nom', 'Résolu');
                })->count(),
            'tickets_en_cours' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('statut', function($query) {
                    $query->where('nom', 'En cours');
                })->count(),
            'tickets_non_assignes' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->whereNull('technicien_id')->count(),
            'temps_moyen_resolution' => $this->getAverageResolutionTime($startDate, $endDate),
            'taux_resolution' => $this->getResolutionRate($startDate, $endDate),
        ];
        
        // Statistiques par technicien
        $techniciensStats = $this->getTechniciensStats($startDate, $endDate);
        
        // Statistiques par plateforme
        $plateformesStats = $this->getPlateformesStats($startDate, $endDate);
        
        // Statistiques par priorité
        $prioritesStats = $this->getPrioritesStats($startDate, $endDate);
        
        return view('support.reports.index', compact(
            'stats',
            'techniciensStats',
            'plateformesStats',
            'prioritesStats',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Exporter les tickets
     */
    public function exportTickets(Request $request)
    {
        // Vérifier que l'utilisateur est superviseur ou administrateur
        if (!in_array(Auth::user()->role->titre, ['Superviseur', 'Administrateur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'excel');
        
        $filename = 'tickets_' . $startDate . '_au_' . $endDate . '.' . $format;
        
        if ($format === 'csv') {
            return Excel::download(new TicketsExport($startDate, $endDate), $filename);
        } else {
            return Excel::download(new TicketsExport($startDate, $endDate), $filename);
        }
    }
    
    /**
     * Exporter les performances des techniciens
     */
    public function exportPerformance(Request $request)
    {
        // Vérifier que l'utilisateur est superviseur ou administrateur
        if (!in_array(Auth::user()->role->titre, ['Superviseur', 'Administrateur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'excel');
        
        $filename = 'performance_techniciens_' . $startDate . '_au_' . $endDate . '.' . $format;
        
        return Excel::download(new PerformanceExport($startDate, $endDate), $filename);
    }
    
    /**
     * Exporter la charge des techniciens
     */
    public function exportTechniciens(Request $request)
    {
        // Vérifier que l'utilisateur est superviseur ou administrateur
        if (!in_array(Auth::user()->role->titre, ['Superviseur', 'Administrateur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'excel');
        
        $filename = 'charge_techniciens_' . $startDate . '_au_' . $endDate . '.' . $format;
        
        return Excel::download(new TechniciensExport($startDate, $endDate), $filename);
    }
    
    /**
     * API pour les données de rapports (graphiques)
     */
    public function apiData(Request $request)
    {
        // Vérifier que l'utilisateur est superviseur ou administrateur
        if (!in_array(Auth::user()->role->titre, ['Superviseur', 'Administrateur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $type = $request->get('type', 'tickets_par_jour');
        
        switch ($type) {
            case 'tickets_par_jour':
                return response()->json($this->getTicketsPerDay($startDate, $endDate));
            case 'tickets_par_plateforme':
                return response()->json($this->getTicketsByPlatform($startDate, $endDate));
            case 'tickets_par_priorite':
                return response()->json($this->getTicketsByPriority($startDate, $endDate));
            case 'performance_techniciens':
                return response()->json($this->getTechniciensPerformance($startDate, $endDate));
            default:
                return response()->json(['error' => 'Type de rapport non valide'], 400);
        }
    }
    
    /**
     * Obtenir le temps moyen de résolution
     */
    private function getAverageResolutionTime($startDate, $endDate)
    {
        $resolvedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('date_fermeture')
            ->whereHas('statut', function($query) {
                $query->where('nom', 'Résolu');
            })
            ->get();
        
        if ($resolvedTickets->isEmpty()) {
            return 'N/A';
        }
        
        $totalMinutes = $resolvedTickets->sum(function($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->date_fermeture);
        });
        
        $averageMinutes = $totalMinutes / $resolvedTickets->count();
        
        if ($averageMinutes < 60) {
            return round($averageMinutes) . ' min';
        } elseif ($averageMinutes < 1440) {
            return round($averageMinutes / 60, 1) . ' heures';
        } else {
            return round($averageMinutes / 1440, 1) . ' jours';
        }
    }
    
    /**
     * Obtenir le taux de résolution
     */
    private function getResolutionRate($startDate, $endDate)
    {
        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        $resolvedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('statut', function($query) {
                $query->where('nom', 'Résolu');
            })->count();
        
        if ($totalTickets === 0) {
            return 0;
        }
        
        return round(($resolvedTickets / $totalTickets) * 100, 1);
    }
    
    /**
     * Obtenir les statistiques des techniciens
     */
    private function getTechniciensStats($startDate, $endDate)
    {
        return User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Administrateur']);
            })
            ->where('etat', true)
            ->withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['tickets as resolved_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statut', function($query) {
                        $query->where('nom', 'Résolu');
                    });
            }])
            ->get()
            ->map(function($technicien) {
                $totalTickets = $technicien->tickets_count;
                $resolvedTickets = $technicien->resolved_tickets_count;
                
                return [
                    'id' => $technicien->id,
                    'nom' => $technicien->nom,
                    'prenom' => $technicien->prenom,
                    'role' => $technicien->role->titre,
                    'total_tickets' => $totalTickets,
                    'resolved_tickets' => $resolvedTickets,
                    'resolution_rate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0,
                    'current_load' => Ticket::where('technicien_id', $technicien->id)
                        ->whereHas('statut', function($query) {
                            $query->where('nom', '!=', 'Résolu');
                        })->count(),
                ];
            });
    }
    
    /**
     * Obtenir les statistiques des plateformes
     */
    private function getPlateformesStats($startDate, $endDate)
    {
        return Plateforme::where('etat', true)
            ->withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($plateforme) {
                return [
                    'id' => $plateforme->id,
                    'nom' => $plateforme->nom,
                    'total_tickets' => $plateforme->tickets_count,
                ];
            });
    }
    
    /**
     * Obtenir les statistiques des priorités
     */
    private function getPrioritesStats($startDate, $endDate)
    {
        return TicketPriorite::withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($priorite) {
                return [
                    'id' => $priorite->id,
                    'nom' => $priorite->nom,
                    'total_tickets' => $priorite->tickets_count,
                    'couleur' => $this->getPriorityColor($priorite->nom),
                ];
            });
    }
    
    /**
     * Obtenir les tickets par jour
     */
    private function getTicketsPerDay($startDate, $endDate)
    {
        $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return $tickets->map(function($ticket) {
            return [
                'date' => $ticket->date,
                'count' => $ticket->count,
            ];
        });
    }
    
    /**
     * Obtenir les tickets par plateforme
     */
    private function getTicketsByPlatform($startDate, $endDate)
    {
        return Plateforme::where('etat', true)
            ->withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($plateforme) {
                return [
                    'name' => $plateforme->nom,
                    'value' => $plateforme->tickets_count,
                ];
            });
    }
    
    /**
     * Obtenir les tickets par priorité
     */
    private function getTicketsByPriority($startDate, $endDate)
    {
        return TicketPriorite::withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($priorite) {
                return [
                    'name' => $priorite->nom,
                    'value' => $priorite->tickets_count,
                ];
            });
    }
    
    /**
     * Obtenir la performance des techniciens
     */
    private function getTechniciensPerformance($startDate, $endDate)
    {
        return User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Administrateur']);
            })
            ->where('etat', true)
            ->withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['tickets as resolved_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statut', function($query) {
                        $query->where('nom', 'Résolu');
                    });
            }])
            ->get()
            ->map(function($technicien) {
                return [
                    'name' => $technicien->nom . ' ' . $technicien->prenom,
                    'total' => $technicien->tickets_count,
                    'resolved' => $technicien->resolved_tickets_count,
                    'rate' => $technicien->tickets_count > 0 ? 
                        round(($technicien->resolved_tickets_count / $technicien->tickets_count) * 100, 1) : 0,
                ];
            });
    }
    
    /**
     * Obtenir la couleur de la priorité
     */
    private function getPriorityColor($priorityName)
    {
        $colors = [
            'Urgent' => '#dc2626',
            'Haute' => '#f59e0b',
            'Moyenne' => '#3b82f6',
            'Basse' => '#10b981',
        ];
        
        return $colors[$priorityName] ?? '#6b7280';
    }
}
