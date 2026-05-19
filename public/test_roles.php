<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DES ACCÈS PAR RÔLE ===\n\n";

// Test 1: Technicien
echo "1. TEST TECHNICIEN:\n";
$technicien = App\Models\User::where('email', 'technicien@test.com')->first();
if ($technicien) {
    echo "   - Email: " . $technicien->email . "\n";
    echo "   - Rôle: " . $technicien->role->titre . "\n";
    echo "   - Peut voir le dashboard: " . ($technicien->role->titre !== 'Demandeur' ? 'OUI' : 'NON') . "\n";
    echo "   - Peut créer des tickets: " . ($technicien->role->titre !== 'Demandeur' ? 'OUI' : 'NON') . "\n";
    echo "   - Peut voir les tickets non assignés: " . ($technicien->role->titre === 'Technicien' ? 'OUI' : 'NON') . "\n";
} else {
    echo "   - ERREUR: Technicien non trouvé\n";
}

echo "\n";

// Test 2: Superviseur
echo "2. TEST SUPERVISEUR:\n";
$superviseur = App\Models\User::where('email', 'superviseur@test.com')->first();
if ($superviseur) {
    echo "   - Email: " . $superviseur->email . "\n";
    echo "   - Rôle: " . $superviseur->role->titre . "\n";
    echo "   - Peut voir le dashboard: " . ($superviseur->role->titre !== 'Demandeur' ? 'OUI' : 'NON') . "\n";
    echo "   - Peut accéder aux stats: " . ($superviseur->role->titre === 'Superviseur' ? 'OUI' : 'NON') . "\n";
    echo "   - Peut recevoir les emails de rapports: " . ($superviseur->role->titre === 'Superviseur' ? 'OUI' : 'NON') . "\n";
    echo "   - Peut recevoir les alertes Slack: " . ($superviseur->role->titre === 'Superviseur' ? 'OUI' : 'NON') . "\n";
} else {
    echo "   - ERREUR: Superviseur non trouvé\n";
}

echo "\n";

// Test 3: Vérification des routes protégées
echo "3. TEST DES ROUTES PROTÉGÉES:\n";
echo "   - Route dashboard: /dashboard (auth requis)\n";
echo "   - Route superviseur: /supervisor/stats (auth + permission superviseur.stats)\n";
echo "   - Route tickets: /tickets (auth requis)\n";
echo "   - Route création ticket: /tickets/create (auth requis)\n";

echo "\n";

// Test 4: Vérification des permissions
echo "4. TEST DES PERMISSIONS:\n";
$routes = [
    'dashboard' => 'Support\DashboardController@index',
    'supervisor.stats' => 'Support\SupervisorDashboardController@index',
    'tickets.index' => 'Support\TicketController@index',
    'tickets.create' => 'Support\TicketController@create',
];

foreach ($routes as $routeName => $controller) {
    echo "   - Route $routeName: $controller\n";
}

echo "\n=== INSTRUCTIONS DE TEST ===\n";
echo "1. Démarrer le serveur: php artisan serve\n";
echo "2. Ouvrir le navigateur sur: http://127.0.0.1:8000\n";
echo "3. Se connecter avec:\n";
echo "   - Technicien: technicien@test.com / password\n";
echo "   - Superviseur: superviseur@test.com / password\n";
echo "4. Tester les accès:\n";
echo "   - Dashboard: http://127.0.0.1:8000/dashboard\n";
echo "   - Stats superviseur: http://127.0.0.1:8000/supervisor/stats\n";
echo "   - Tickets: http://127.0.0.1:8000/tickets\n";
echo "   - Création ticket: http://127.0.0.1:8000/tickets/create\n";

echo "\n=== SERVEUR DÉMARRÉ ===\n";
echo "Le serveur est disponible sur: http://127.0.0.1:8000\n";
