<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plateforme;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\TicketCategorie;
use App\Models\TicketPriorite;
use App\Models\TicketStatut;

echo "Création des données de base...\n";

// 1. Créer les rôles
echo "\n=== Création des rôles ===\n";
$roles = [
    ['titre' => 'Demandeur'],
    ['titre' => 'Technicien'],
    ['titre' => 'Superviseur'],
    ['titre' => 'Administrateur'],
];

foreach ($roles as $roleData) {
    $role = Role::firstOrCreate(['titre' => $roleData['titre']]);
    echo "- Rôle créé: {$role->titre}\n";
}

// 2. Créer les plateformes
echo "\n=== Création des plateformes ===\n";
$plateformes = [
    [
        'nom' => 'ERP System',
        'code' => 'ERP',
        'cle_api' => 'erp-api-key',
        'secret_key' => 'erp-secret-key',
        'description' => 'Système ERP principal',
        'etat' => true,
        'couleur' => '#3b82f6'
    ],
    [
        'nom' => 'CRM System',
        'code' => 'CRM',
        'cle_api' => 'crm-api-key',
        'secret_key' => 'crm-secret-key',
        'description' => 'Système CRM',
        'etat' => true,
        'couleur' => '#10b981'
    ],
    [
        'nom' => 'RH System',
        'code' => 'RH',
        'cle_api' => 'rh-api-key',
        'secret_key' => 'rh-secret-key',
        'description' => 'Système RH',
        'etat' => true,
        'couleur' => '#f59e0b'
    ],
];

foreach ($plateformes as $plateformeData) {
    $plateforme = Plateforme::firstOrCreate(['code' => $plateformeData['code']], $plateformeData);
    echo "- Plateforme créée: {$plateforme->nom} ({$plateforme->code})\n";
}

// 3. Créer les modules pour chaque plateforme
echo "\n=== Création des modules ===\n";
$modules = [
    // Modules ERP
    ['plateforme_id' => 1, 'nom' => 'Gestion Comptable', 'code' => 'GC', 'etat' => true],
    ['plateforme_id' => 1, 'nom' => 'Gestion Stock', 'code' => 'GS', 'etat' => true],
    ['plateforme_id' => 1, 'nom' => 'Facturation', 'code' => 'FAC', 'etat' => true],
    
    // Modules CRM
    ['plateforme_id' => 2, 'nom' => 'Gestion Clients', 'code' => 'GC', 'etat' => true],
    ['plateforme_id' => 2, 'nom' => 'Ventes', 'code' => 'VENT', 'etat' => true],
    ['plateforme_id' => 2, 'nom' => 'Marketing', 'code' => 'MKT', 'etat' => true],
    
    // Modules RH
    ['plateforme_id' => 3, 'nom' => 'Gestion Employés', 'code' => 'GE', 'etat' => true],
    ['plateforme_id' => 3, 'nom' => 'Paie', 'code' => 'PAIE', 'etat' => true],
    ['plateforme_id' => 3, 'nom' => 'Congés', 'code' => 'CONG', 'etat' => true],
];

foreach ($modules as $moduleData) {
    $module = Module::firstOrCreate(['code' => $moduleData['code']], $moduleData);
    $plateforme = Plateforme::find($module->plateforme_id);
    echo "- Module créé: {$module->nom} pour {$plateforme->nom}\n";
}

// 4. Créer les catégories de tickets
echo "\n=== Création des catégories de tickets ===\n";
$categories = [
    ['nom' => 'Incident technique'],
    ['nom' => 'Demande d\'assistance'],
    ['nom' => 'Problème de connexion'],
    ['nom' => 'Erreur système'],
    ['nom' => 'Demande d\'information'],
    ['nom' => 'Amélioration'],
    ['nom' => 'Bug'],
    ['nom' => 'Autre'],
];

foreach ($categories as $categorieData) {
    $categorie = TicketCategorie::firstOrCreate(['nom' => $categorieData['nom']]);
    echo "- Catégorie créée: {$categorie->nom}\n";
}

// 5. Créer les priorités
echo "\n=== Création des priorités ===\n";
$priorites = [
    ['nom' => 'Basse', 'niveau' => 1],
    ['nom' => 'Normale', 'niveau' => 2],
    ['nom' => 'Haute', 'niveau' => 3],
    ['nom' => 'Urgente', 'niveau' => 4],
];

foreach ($priorites as $prioriteData) {
    $priorite = TicketPriorite::firstOrCreate(['nom' => $prioriteData['nom']], $prioriteData);
    echo "- Priorité créée: {$priorite->nom} (niveau {$priorite->niveau})\n";
}

// 6. Créer les statuts
echo "\n=== Création des statuts ===\n";
$statuts = [
    ['nom' => 'Ouvert', 'code' => 'ouvert'],
    ['nom' => 'En cours', 'code' => 'en-cours'],
    ['nom' => 'En attente', 'code' => 'en-attente'],
    ['nom' => 'Résolu', 'code' => 'resolu'],
    ['nom' => 'Fermé', 'code' => 'ferme'],
];

foreach ($statuts as $statutData) {
    $statut = TicketStatut::firstOrCreate(['code' => $statutData['code']], $statutData);
    echo "- Statut créé: {$statut->nom} ({$statut->code})\n";
}

// 7. Créer un utilisateur administrateur
echo "\n=== Création utilisateur administrateur ===\n";
$adminRole = Role::where('titre', 'Administrateur')->first();
$adminUser = User::firstOrCreate(
    ['email' => 'admin@support.com'],
    [
        'nom' => 'Admin',
        'prenom' => 'System',
        'password' => bcrypt('admin123'),
        'telephone' => '0123456789',
        'plateforme_id' => 1,
        'externe_id' => 'ADMIN001',
        'role_id' => $adminRole->id,
        'etat' => true,
    ]
);
echo "- Utilisateur admin créé: {$adminUser->email}\n";

echo "\n=== Données de base créées avec succès ! ===\n";
echo "\nIdentifiants de connexion:\n";
echo "Email: admin@support.com\n";
echo "Mot de passe: admin123\n";
echo "\nURL de connexion: http://127.0.0.1:8000/login\n";
