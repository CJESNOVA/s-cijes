<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;

echo "Création de notifications de test pour l'administrateur...\n";

// Trouver l'utilisateur administrateur
$adminUser = User::where('email', 'admin@support.com')->first();

if (!$adminUser) {
    echo "Utilisateur administrateur non trouvé !\n";
    exit(1);
}

echo "Utilisateur trouvé: {$adminUser->nom} {$adminUser->prenom}\n";

// Créer plusieurs notifications de test
$notifications = [
    [
        'type' => 'ticket_created',
        'title' => 'Nouveau ticket créé',
        'message' => 'Un nouveau ticket TCK-2026-TEST001 a été créé par Jean Dupont. Le ticket concerne un problème de connexion au système ERP.',
    ],
    [
        'type' => 'urgent_ticket',
        'title' => 'Ticket urgent',
        'message' => 'Un ticket urgent a été créé par Marie Martin. Le problème bloque toute l\'équipe de comptabilité.',
    ],
    [
        'type' => 'message_added',
        'title' => 'Nouveau message',
        'message' => 'Vous avez reçu un nouveau message de Pierre Durand concernant le ticket TCK-2026-00042.',
    ],
    [
        'type' => 'ticket_resolved',
        'title' => 'Ticket résolu',
        'message' => 'Le ticket TCK-2026-00038 a été résolu par Sophie Bernard. Le problème de connexion a été corrigé.',
    ],
    [
        'type' => 'system_maintenance',
        'title' => 'Maintenance système',
        'message' => 'Une maintenance est prévue ce soir de 22h à 23h. Le système CRM sera indisponible pendant cette période.',
    ],
];

foreach ($notifications as $index => $notifData) {
    $notification = Notification::createSimpleNotification(
        $adminUser,
        $notifData['type'],
        $notifData['title'],
        $notifData['message'],
        [
            'test' => true,
            'index' => $index + 1,
            'created_at' => now()->subMinutes($index * 5)->toDateTimeString()
        ]
    );
    
    // Marquer certaines notifications comme lues
    if ($index >= 2) {
        $notification->markAsRead();
    }
    
    echo "- Notification créée: {$notification->title} (" . ($notification->read ? 'Lue' : 'Non lue') . ")\n";
}

echo "\n=== Notifications de test créées avec succès ! ===\n";
echo "Total: " . count($notifications) . " notifications\n";
echo "Non lues: " . $adminUser->unreadNotifications()->count() . "\n";
echo "Lues: " . $adminUser->notifications()->where('read', true)->count() . "\n";
echo "\nURL de test: http://127.0.0.1:8000/notifications\n";
echo "\nConnectez-vous avec:\n";
echo "Email: admin@support.com\n";
echo "Mot de passe: admin123\n";
