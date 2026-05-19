<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Plateforme;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        // Récupérer la plateforme CIJET
        $plateforme = Plateforme::where('code', 'CIJET')->first();
        
        if (!$plateforme) {
            $this->command->error('Plateforme CIJET non trouvée. Veuillez exécuter PlateformeSeeder d\'abord.');
            return;
        }

        $modules = [
            [
                'nom' => 'Gestion des utilisateurs',
                'code' => 'gestion-utilisateurs',
                'etat' => true,
            ],
            [
                'nom' => 'Rapports et statistiques',
                'code' => 'rapports-statistiques',
                'etat' => true,
            ],
            [
                'nom' => 'Facturation',
                'code' => 'facturation',
                'etat' => true,
            ],
            [
                'nom' => 'Messagerie interne',
                'code' => 'messagerie-interne',
                'etat' => true,
            ],
            [
                'nom' => 'Gestion de projet',
                'code' => 'gestion-projet',
                'etat' => true,
            ],
            [
                'nom' => 'Stock et inventaire',
                'code' => 'stock-inventaire',
                'etat' => true,
            ],
            [
                'nom' => 'CRM',
                'code' => 'crm',
                'etat' => true,
            ],
            [
                'nom' => 'Ressources humaines',
                'code' => 'ressources-humaines',
                'etat' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['nom' => $module['nom'], 'plateforme_id' => $plateforme->id],
                [
                    'code' => $module['code'],
                    'etat' => $module['etat'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Modules créés avec succès pour la plateforme CIJET.');
    }
}
