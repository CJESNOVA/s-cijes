<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KnowledgeBaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Guide de démarrage',
                'slug' => 'guide-demarrage',
                'description' => 'Articles pour aider les nouveaux utilisateurs à démarrer',
                'color' => '#10B981',
                'icon' => 'fas fa-rocket',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Résolution de problèmes',
                'slug' => 'resolution-problemes',
                'description' => 'Solutions aux problèmes courants',
                'color' => '#F59E0B',
                'icon' => 'fas fa-tools',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Procédures',
                'slug' => 'procedures',
                'description' => 'Procédures et workflows standards',
                'color' => '#3B82F6',
                'icon' => 'fas fa-list-check',
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Configuration',
                'slug' => 'configuration',
                'description' => 'Guides de configuration et paramètres',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-cog',
                'active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Sécurité',
                'slug' => 'securite',
                'description' => 'Politiques de sécurité et bonnes pratiques',
                'color' => '#EF4444',
                'icon' => 'fas fa-shield-alt',
                'active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Intégrations',
                'slug' => 'integrations',
                'description' => 'Guides d\'intégration avec d\'autres systèmes',
                'color' => '#06B6D4',
                'icon' => 'fas fa-plug',
                'active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\KnowledgeBaseCategory::create($category);
        }
    }
}
