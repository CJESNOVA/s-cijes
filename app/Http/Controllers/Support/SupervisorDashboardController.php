<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Plateforme;
use Illuminate\Support\Facades\DB;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        // 1. Chiffres globaux
        $stats = [
            'total' => Ticket::count(),
            'ouverts' => Ticket::whereIn('statut_id', [1, 2, 3])->count(), // Ouvert, Affecté, En cours
            'resolus' => Ticket::where('statut_id', 5)->count(),
        ];

        // 2. Volume par plateforme avec calcul du pourcentage
        $parPlateforme = Plateforme::withCount('tickets')
            ->get()
            ->map(function ($plat) use ($stats) {
                $plat->pourcentage = $stats['total'] > 0 
                    ? ($plat->tickets_count / $stats['total']) * 100 
                    : 0;
                return $plat;
            });

        // 3. Temps Moyen de Résolution (SLA) en heures
        $tempsMoyenHeures = Ticket::whereNotNull('date_fermeture')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, date_ouverture, date_fermeture)) as average_time'))
            ->first()->average_time ?? 0;

        return view('support.supervisor.dashboard', compact('stats', 'parPlateforme', 'tempsMoyenHeures'));
    }
}
