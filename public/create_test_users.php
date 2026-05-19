<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Créer un technicien de test
$technicien = App\Models\User::create([
    'nom' => 'Technicien',
    'prenom' => 'Test',
    'email' => 'technicien@test.com',
    'telephone' => '123456789',
    'externe_id' => 'TECH_001',
    'password' => Hash::make('password'),
    'role_id' => 3,
    'plateforme_id' => 1,
    'etat' => true
]);
echo "Technicien créé: " . $technicien->email . "\n";

// Créer un superviseur de test
$superviseur = App\Models\User::create([
    'nom' => 'Superviseur',
    'prenom' => 'Test',
    'email' => 'superviseur@test.com',
    'telephone' => '987654321',
    'externe_id' => 'SUP_001',
    'password' => Hash::make('password'),
    'role_id' => 2,
    'plateforme_id' => 1,
    'etat' => true
]);
echo "Superviseur créé: " . $superviseur->email . "\n";

echo "Utilisateurs de test créés avec succès !\n";
echo "Technicien: technicien@test.com / password\n";
echo "Superviseur: superviseur@test.com / password\n";
