<?php
require '../vendor/autoload.php';

use Firebase\JWT\JWT;

// --- CONFIGURATION (Doit matcher notre système) ---
$secret_key = "cjes-support-secret-key-2024-very-long-key-for-hmac-sha256-encoding"; 
$plateforme_code = "ERP"; // Utiliser une plateforme qui existe dans notre base
$callback_url = "http://127.0.0.1:8000/sso/callback";

// --- DONNÉES DE L'UTILISATEUR FICTIF ---
$payload = [
    "user_id" => "EXT001", // ID externe dans le système source
    "nom" => "Koffi",
    "prenom" => "Jean",
    "email" => "jean.koffi@cjes.com",
    "plateforme_code" => $plateforme_code,
    "telephone" => "+22507070707",
    "role" => "Demandeur", // Rôle dans le système source
    "iat" => time(),            // Émis à
    "exp" => time() + (60 * 5)  // Expire dans 5 minutes
];

// --- GÉNÉRATION DU TOKEN ---
$jwt = JWT::encode($payload, $secret_key, 'HS256');

// --- GÉNÉRATION DU LIEN DE TEST ---
$final_url = $callback_url . "?token=" . $jwt;

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Simulateur SSO CJES Support</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .container { background: #f8f9fa; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; text-align: center; margin-bottom: 30px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .token { background: #f3f4f6; padding: 15px; border-radius: 5px; word-break: break-all; font-family: monospace; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 30px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s; }
        .btn:hover { background: #1d4ed8; }
        .user-info { background: #fff; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2563eb; }
        .field { margin: 10px 0; }
        .label { font-weight: bold; color: #374151; }
        .value { color: #6b7280; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Simulateur SSO CJES Support</h1>
        
        <div class='info'>
            <strong>Configuration utilisée:</strong>
            <ul>
                <li>Plateforme: <strong>$plateforme_code</strong></li>
                <li>Secret Key: <strong>$secret_key</strong></li>
                <li>Callback: <strong>$callback_url</strong></li>
            </ul>
        </div>

        <div class='user-info'>
            <h3>Utilisateur simulé:</h3>
            <div class='field'>
                <span class='label'>Nom complet:</span>
                <span class='value'>{$payload['nom']} {$payload['prenom']}</span>
            </div>
            <div class='field'>
                <span class='label'>Email:</span>
                <span class='value'>{$payload['email']}</span>
            </div>
            <div class='field'>
                <span class='label'>ID Externe:</span>
                <span class='value'>{$payload['user_id']}</span>
            </div>
            <div class='field'>
                <span class='label'>Plateforme:</span>
                <span class='value'>{$payload['plateforme_code']}</span>
            </div>
            <div class='field'>
                <span class='label'>Rôle:</span>
                <span class='value'>{$payload['role']}</span>
            </div>
        </div>

        <div class='token'>
            <strong>Token JWT généré:</strong><br>
            <code>$jwt</code>
        </div>

        <div style='text-align: center; margin: 30px 0;'>
            <a href='$final_url' class='btn'>
                Simuler le clic 'Besoin de Support' depuis CIJET
            </a>
        </div>

        <div class='info'>
            <strong>Notes:</strong>
            <ul>
                <li>Ce simulateur crée un token JWT valide pour tester l'authentification SSO</li>
                <li>L'utilisateur sera créé automatiquement s'il n'existe pas</li>
                <li>Le token expire dans 5 minutes</li>
                <li>Après connexion, vous pourrez tester toutes les fonctionnalités</li>
            </ul>
        </div>
    </div>
</body>
</html>";
