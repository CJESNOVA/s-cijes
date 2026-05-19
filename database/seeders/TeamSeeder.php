<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs
        $superviseurs = \App\Models\User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Superviseur');
        })->get();
        
        $techniciens = \App\Models\User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Technicien');
        })->get();

        if ($superviseurs->isEmpty() || $techniciens->isEmpty()) {
            return;
        }

        $teams = [
            [
                'name' => 'Équipe Support Niveau 1',
                'description' => 'Gestion des tickets de base et premier niveau de support',
                'color' => '#3B82F6', // Bleu
                'supervisor_id' => $superviseurs->first()->id,
                'members' => $techniciens->take(3)->pluck('id')->toArray(),
                'leader_id' => $techniciens->first()->id,
            ],
            [
                'name' => 'Équipe Support Avancé',
                'description' => 'Traitement des tickets complexes et escalades',
                'color' => '#10B981', // Vert
                'supervisor_id' => $superviseurs->first()->id,
                'members' => $techniciens->skip(3)->take(2)->pluck('id')->toArray(),
                'leader_id' => $techniciens->skip(3)->first()->id,
            ],
            [
                'name' => 'Équipe Infrastructure',
                'description' => 'Maintenance et gestion des systèmes',
                'color' => '#F59E0B', // Orange
                'supervisor_id' => $superviseurs->count() > 1 ? $superviseurs[1]->id : $superviseurs->first()->id,
                'members' => $techniciens->skip(5)->take(2)->pluck('id')->toArray(),
                'leader_id' => $techniciens->skip(5)->first()->id,
            ],
        ];

        foreach ($teams as $teamData) {
            $team = \App\Models\Team::create([
                'name' => $teamData['name'],
                'description' => $teamData['description'],
                'color' => $teamData['color'],
                'supervisor_id' => $teamData['supervisor_id'],
                'active' => true,
            ]);

            // Ajouter les membres
            foreach ($teamData['members'] as $memberId) {
                $role = ($memberId == $teamData['leader_id']) ? 'leader' : 'member';
                $user = \App\Models\User::find($memberId);
                if ($user) {
                    $team->addMember($user, $role);
                }
            }
        }
    }
}
