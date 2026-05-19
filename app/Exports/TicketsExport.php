<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class TicketsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
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
        return Ticket::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['user', 'technicien', 'technicien.role', 'plateforme', 'priorite', 'statut', 'categorie'])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Titre',
            'Description',
            'Demandeur',
            'Email Demandeur',
            'Plateforme',
            'Module',
            'Catégorie',
            'Priorité',
            'Statut',
            'Technicien Assigné',
            'Date de Création',
            'Date de Fermeture',
            'Délai de Réponse (minutes)',
            'Temps de Traitement (heures)',
        ];
    }

    public function map($ticket): array
    {
        $processingTime = null;
        if ($ticket->date_fermeture) {
            $processingTime = $ticket->created_at->diffInHours($ticket->date_fermeture);
        }

        return [
            $ticket->reference,
            $ticket->titre,
            strip_tags($ticket->description),
            $ticket->user->nom . ' ' . $ticket->user->prenom,
            $ticket->user->email,
            $ticket->plateforme->nom,
            $ticket->module->nom ?? 'N/A',
            $ticket->categorie->nom ?? 'N/A',
            $ticket->priorite->nom,
            $ticket->statut->nom,
            $ticket->technicien ? $ticket->technicien->nom . ' ' . $ticket->technicien->prenom : 'Non assigné',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->date_fermeture ? $ticket->date_fermeture->format('d/m/Y H:i') : 'N/A',
            $ticket->delai_reponse_minutes ?? 'N/A',
            $processingTime ? $processingTime . 'h' : 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Tickets du ' . $this->startDate . ' au ' . $this->endDate;
    }
}
