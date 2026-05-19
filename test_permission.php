<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DES PERMISSIONS ===\n\n";

// Test 1: Vérifier l'utilisateur superviseur
echo "1. TEST UTILISATEUR SUPERVISEUR:\n";
$superviseur = App\Models\User::where('email', 'superviseur@test.com')->first();

if ($superviseur) {
    echo "   - Email: " . $superviseur->email . "\n";
    echo "   - Rôle: " . $superviseur->role->titre . "\n";
    echo "   - Permission access-supervisor-stats: " . ($superviseur->hasPermission('access-supervisor-stats') ? 'OUI' : 'NON') . "\n";
} else {
    echo "   - ERREUR: Superviseur non trouvé\n";
}

echo "\n";

// Test 2: Vérifier l'utilisateur technicien
echo "2. TEST UTILISATEUR TECHNICIEN:\n";
$technicien = App\Models\User::where('email', 'technicien@test.com')->first();

if ($technicien) {
    echo "   - Email: " . $technicien->email . "\n";
    echo "   - Rôle: " . $technicien->role->titre . "\n";
    echo "   - Permission access-supervisor-stats: " . ($technicien->hasPermission('access-supervisor-stats') ? 'OUI' : 'NON') . "\n";
} else {
    echo "   - ERREUR: Technicien non trouvé\n";
}

echo "\n";

// Test 3: Vérifier les permissions disponibles
echo "3. PERMISSIONS DISPONIBLES:\n";
$permissions = [
    'access-supervisor-stats' => ['Superviseur', 'Administrateur'],
    'manage-tickets' => ['Technicien', 'Superviseur', 'Administrateur'],
    'view-all-tickets' => ['Technicien', 'Superviseur', 'Administrateur'],
];

foreach ($permissions as $permission => $roles) {
    echo "   - $permission: " . implode(', ', $roles) . "\n";
}

echo "\n=== TEST TERMINÉ ===\n";
