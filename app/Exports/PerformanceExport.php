<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class PerformanceExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Administrateur']);
            })
            ->where('etat', true)
            ->with(['role', 'plateforme'])
            ->withCount(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['tickets as resolved_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statut', function($query) {
                        $query->where('nom', 'Résolu');
                    });
            }])
            ->withCount(['tickets as high_priority_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('priorite', function($query) {
                        $query->where('nom', 'Urgent');
                    });
            }])
            ->with(['tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotNull('date_fermeture')
                    ->orderBy('created_at');
            }])
            ->orderBy('resolved_tickets', 'desc');
    }

    public function headings(): array
    {
        return [
            'Technicien',
            'Rôle',
            'Plateforme',
            'Total Tickets',
            'Tickets Résolus',
            'Tickets Urgents',
            'Taux de Résolution (%)',
            'Temps Moyen Résolution',
            'Tickets/Jour (Moyenne)',
            'Performance Score',
            'Détails Performance',
        ];
    }

    public function map($technicien): array
    {
        $totalTickets = $technicien->tickets_count;
        $resolvedTickets = $technicien->resolved_tickets_count;
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0;
        
        // Calculer le temps moyen de résolution
        $avgResolutionTime = $this->getAverageResolutionTime($technicien);
        
        // Calculer les tickets par jour
        $daysDiff = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        $ticketsPerDay = $daysDiff > 0 ? round($totalTickets / $daysDiff, 1) : 0;
        
        // Calculer le score de performance
        $performanceScore = $this->calculatePerformanceScore($resolutionRate, $avgResolutionTime, $technicien->high_priority_tickets_count);
        
        // Détails de performance
        $performanceDetails = $this->getPerformanceDetails($resolutionRate, $avgResolutionTime, $technicien->high_priority_tickets_count);

        return [
            $technicien->nom . ' ' . $technicien->prenom,
            $technicien->role->titre,
            $technicien->plateforme->nom ?? 'N/A',
            $totalTickets,
            $resolvedTickets,
            $technicien->high_priority_tickets_count,
            $resolutionRate,
            $avgResolutionTime,
            $ticketsPerDay,
            $performanceScore,
            $performanceDetails,
        ];
    }

    public function title(): string
    {
        return 'Rapport de Performance du ' . $this->startDate . ' au ' . $this->endDate;
    }

    private function getAverageResolutionTime($technicien)
    {
        $resolvedTickets = $technicien->tickets
            ->whereNotNull('date_fermeture')
            ->whereHas('statut', function($query) {
                $query->where('nom', 'Résolu');
            });

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
            return round($averageMinutes / 60, 1) . ' h';
        } else {
            return round($averageMinutes / 1440, 1) . ' j';
        }
    }

    private function calculatePerformanceScore($resolutionRate, $avgResolutionTime, $highPriorityCount)
    {
        $score = 0;
        
        // Taux de résolution (40% du score)
        $score += ($resolutionRate / 100) * 40;
        
        // Temps de résolution (30% du score)
        if ($avgResolutionTime !== 'N/A') {
            $timeInHours = $this->convertToHours($avgResolutionTime);
            if ($timeInHours <= 2) {
                $score += 30;
            } elseif ($timeInHours <= 8) {
                $score += 20;
            } elseif ($timeInHours <= 24) {
                $score += 10;
            }
        }
        
        // Gestion des urgents (30% du score)
        if ($highPriorityCount === 0) {
            $score += 30;
        } elseif ($highPriorityCount <= 2) {
            $score += 20;
        } elseif ($highPriorityCount <= 5) {
            $score += 10;
        }
        
        return round($score, 1);
    }

    private function getPerformanceDetails($resolutionRate, $avgResolutionTime, $highPriorityCount)
    {
        $details = [];
        
        if ($resolutionRate >= 90) {
            $details[] = 'Excellent taux de résolution';
        } elseif ($resolutionRate >= 70) {
            $details[] = 'Bon taux de résolution';
        } else {
            $details[] = 'Taux de résolution à améliorer';
        }
        
        if ($avgResolutionTime !== 'N/A') {
            $timeInHours = $this->convertToHours($avgResolutionTime);
            if ($timeInHours <= 2) {
                $details[] = 'Résolution rapide';
            } elseif ($timeInHours <= 8) {
                $details[] = 'Résolution acceptable';
            } else {
                $details[] = 'Résolution lente';
            }
        }
        
        if ($highPriorityCount === 0) {
            $details[] = 'Aucun urgent en attente';
        } elseif ($highPriorityCount <= 2) {
            $details[] = 'Gestion des urgents correcte';
        } else {
            $details[] = 'Trop d\'urgents en attente';
        }
        
        return implode(', ', $details);
    }

    private function convertToHours($timeString)
    {
        if (strpos($timeString, 'min') !== false) {
            return (float)str_replace(' min', '', $timeString) / 60;
        } elseif (strpos($timeString, 'h') !== false) {
            return (float)str_replace(' h', '', $timeString);
        } elseif (strpos($timeString, 'j') !== false) {
            return (float)str_replace(' j', '', $timeString) * 24;
        }
        return 999; // Valeur élevée pour "N/A"
    }
}
