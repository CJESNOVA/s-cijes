<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plateforme;

echo "Mise à jour des clés secrètes des plateformes...\n";

// Mettre à jour la plateforme ERP avec la clé du simulateur
$erp = Plateforme::where('code', 'ERP')->first();
if ($erp) {
    $erp->update(['secret_key' => 'cjes-support-secret-key-2024-very-long-key-for-hmac-sha256-encoding']);
    echo "- Plateforme ERP mise à jour avec la clé secrète du simulateur\n";
} else {
    echo "- Plateforme ERP non trouvée\n";
}

// Mettre à jour les autres plateformes avec des clés de test
$platforms = [
    'CRM' => 'crm-secret-key-2024-very-long-key-for-hmac-sha256-encoding',
    'RH' => 'rh-secret-key-2024-very-long-key-for-hmac-sha256-encoding',
];

foreach ($platforms as $code => $secretKey) {
    $platform = Plateforme::where('code', $code)->first();
    if ($platform) {
        $platform->update(['secret_key' => $secretKey]);
        echo "- Plateforme $code mise à jour avec la clé: $secretKey\n";
    } else {
        echo "- Plateforme $code non trouvée\n";
    }
}

echo "\n=== Clés secrètes mises à jour avec succès ! ===\n";
echo "\nVous pouvez maintenant tester le SSO avec:\n";
echo "URL: http://127.0.0.1:8000/public/test-sso.php\n";
echo "L'utilisateur sera créé automatiquement avec le rôle 'Demandeur'\n";
