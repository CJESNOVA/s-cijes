<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $roles = [
        ['titre' => 'Administrateur', 'description' => 'Accès total au système'],
        ['titre' => 'Superviseur', 'description' => 'Gestion des techniciens et rapports'],
        ['titre' => 'Technicien', 'description' => 'Traitement des tickets'],
        ['titre' => 'Demandeur', 'description' => 'Utilisateur final des plateformes'],
    ];

    foreach ($roles as $role) {
        \App\Models\Role::firstOrCreate(['titre' => $role['titre']], $role);
    }
}
}
