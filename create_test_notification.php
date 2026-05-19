<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Plateforme;

// Créer les données de base nécessaires
$plateforme = Plateforme::first();
if (!$plateforme) {
    $plateforme = Plateforme::create([
        'nom' => 'Test Platform',
        'code' => 'TEST',
        'cle_api' => 'test-api-key',
        'secret_key' => 'test-secret-key',
        'description' => 'Platform de test',
        'etat' => true,
        'couleur' => '#3b82f6'
    ]);
}

// Créer un utilisateur de test s'il n'existe pas
$user = User::first();
if (!$user) {
    $role = Role::where('titre', 'Administrateur')->first();
    if (!$role) {
        $role = Role::create(['titre' => 'Administrateur']);
    }
    
    $user = User::create([
        'nom' => 'Test',
        'prenom' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'plateforme_id' => 1,
        'externe_id' => 'TEST001',
        'role_id' => $role->id,
        'etat' => true,
    ]);
    
    echo "Utilisateur de test créé:\n";
    echo "- ID: {$user->id}, Nom: {$user->nom} {$user->prenom}, Role: {$user->role->titre}\n";
} else {
    echo "Utilisateur existant:\n";
    echo "- ID: {$user->id}, Nom: {$user->nom} {$user->prenom}, Role: {$user->role->titre}\n";
}

if ($user) {
    $notification = Notification::createSimpleNotification(
        $user,
        'ticket_created',
        'Ticket de test créé',
        'Ceci est une notification de test pour vérifier le fonctionnement du système. Vous pouvez cliquer sur les actions pour tester les fonctionnalités.',
        [
            'test' => true,
            'created_at' => now()->toDateTimeString()
        ]
    );
    
    echo "Notification de test créée avec l'ID: {$notification->id}\n";
    echo "Pour l'utilisateur: {$user->nom} {$user->prenom}\n";
    echo "URL de test: http://127.0.0.1:8000/notifications\n";
} else {
    echo "Utilisateur non trouvé. Vérifie l'ID utilisateur.\n";
}
