<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TicketCategorie;

class TicketCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Problème de connexion',
            'Erreur technique',
            'Demande de fonctionnalité',
            'Question d\'utilisation',
            'Performance',
            'Facturation',
            'Données et export',
            'Intégration',
            'Sécurité',
            'Autre',
        ];

        foreach ($categories as $categorie) {
            TicketCategorie::firstOrCreate(
                ['nom' => $categorie],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Catégories de tickets créées avec succès.');
    }
}
