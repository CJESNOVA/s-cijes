<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TechniciensExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
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
            ->withCount(['tickets as pending_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statut', function($query) {
                        $query->where('nom', 'En cours');
                    });
            }])
            ->withCount(['tickets as high_priority_tickets' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('priorite', function($query) {
                        $query->where('nom', 'Urgent');
                    });
            }])
            ->orderBy('resolved_tickets', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Rôle',
            'Plateforme',
            'Total Tickets',
            'Tickets Résolus',
            'Tickets en Cours',
            'Tickets Urgents',
            'Taux de Résolution (%)',
            'Charge Actuelle',
            'Dernière Connexion',
            'État',
        ];
    }

    public function map($technicien): array
    {
        $totalTickets = $technicien->tickets_count;
        $resolvedTickets = $technicien->resolved_tickets_count;
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0;
        
        $currentLoad = Ticket::where('technicien_id', $technicien->id)
            ->whereHas('statut', function($query) {
                $query->where('nom', '!=', 'Résolu');
            })->count();

        return [
            $technicien->id,
            $technicien->nom,
            $technicien->prenom,
            $technicien->email,
            $technicien->telephone ?? 'N/A',
            $technicien->role->titre,
            $technicien->plateforme->nom ?? 'N/A',
            $totalTickets,
            $resolvedTickets,
            $technicien->pending_tickets_count,
            $technicien->high_priority_tickets_count,
            $resolutionRate,
            $currentLoad,
            $technicien->last_login_at ? $technicien->last_login_at->format('d/m/Y H:i') : 'Jamais',
            $technicien->etat ? 'Actif' : 'Inactif',
        ];
    }

    public function title(): string
    {
        return 'Charge des Techniciens du ' . $this->startDate . ' au ' . $this->endDate;
    }
}
