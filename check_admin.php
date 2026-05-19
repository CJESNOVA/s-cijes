<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@support.com')->first();
if ($user) {
    echo 'Utilisateur trouvé: ' . $user->nom . ' ' . $user->prenom . PHP_EOL;
    echo 'Rôle: ' . $user->role->titre . PHP_EOL;
    echo 'ID: ' . $user->id . PHP_EOL;
    echo 'Email: ' . $user->email . PHP_EOL;
    echo 'État: ' . ($user->etat ? 'Actif' : 'Inactif') . PHP_EOL;
} else {
    echo 'Utilisateur admin@support.com non trouvé' . PHP_EOL;
}
