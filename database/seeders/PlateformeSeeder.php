<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlateformeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\Plateforme::firstOrCreate(
        ['code' => 'CIJET'],
        [
            'nom' => 'CIJET Support',
            'cle_api' => Str::random(32),
            'secret_key' => bin2hex(random_bytes(32)),
            'etat' => true,
        ]
    );
    
    // Ajoute ici CEPROSAT ou d'autres systèmes sources
}
}
