<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ticket;
use App\Mail\DailyStatsReport;
use Illuminate\Support\Facades\Mail;

class SendDailyStats extends Command
{
    protected $signature = 'support:send-daily-report';
    protected $description = 'Envoie les statistiques de la veille aux superviseurs';

    public function handle()
    {
        // Extraction des stats des dernières 24h
        $stats = [
            'nouveaux' => Ticket::whereDate('created_at', today()->subDay())->count(),
            'resolus'  => Ticket::whereDate('date_fermeture', today()->subDay())->count(),
            'ouverts_total' => Ticket::whereIn('statut_id', [1,2,3])->count(),
        ];

        // Récupération des superviseurs
        $superviseurs = User::whereHas('role', fn($q) => $q->where('titre', 'Superviseur'))->get();

        foreach ($superviseurs as $superviseur) {
            Mail::to($superviseur->email)->send(new DailyStatsReport($stats));
        }

        $this->info('Rapports envoyés avec succès.');
    }
}
