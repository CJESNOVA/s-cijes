<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Statuts
    $statuts = ['Ouvert', 'Affecté', 'En cours', 'En attente', 'Résolu', 'Fermé'];
    foreach ($statuts as $s) {
        \App\Models\TicketStatut::firstOrCreate(
            ['nom' => $s],
            ['code' => Str::slug($s)]
        );
    }

    // Priorités (avec impact SLA)
    \App\Models\TicketPriorite::firstOrCreate(['nom' => 'Urgent'], ['niveau' => 3, 'temps_max_heures' => 4]);
    \App\Models\TicketPriorite::firstOrCreate(['nom' => 'Normal'], ['niveau' => 2, 'temps_max_heures' => 24]);
    \App\Models\TicketPriorite::firstOrCreate(['nom' => 'Faible'], ['niveau' => 1, 'temps_max_heures' => 72]);
}
}
