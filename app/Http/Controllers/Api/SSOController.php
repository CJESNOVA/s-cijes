<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plateforme;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;

class SSOController extends Controller
{
    public function callback(Request $request)
    {
        // 1. Récupération du token depuis l'URL (ex: ?token=xxxx)
        $token = $request->query('token');
        if (!$token) return response()->json(['error' => 'Token manquant'], 400);

        try {
            // 2. Pré-décodage non vérifié pour identifier la plateforme
            // On décode sans vérifier juste pour lire le "plateforme_code" dans le payload
            $tks = explode('.', $token);
            $payload = json_decode(base64_decode($tks[1]));
            
            $plateforme = Plateforme::where('code', $payload->plateforme_code)->firstOrFail();

            // 3. Vérification réelle de la signature avec la clé secrète de la plateforme
            $decoded = JWT::decode($token, new Key($plateforme->secret_key, 'HS256'));

            // 4. Stratégie "Get or Create" (Auto-provisioning)
            // On cherche l'utilisateur par son ID externe ET sa plateforme
            $user = User::updateOrCreate(
                [
                    'plateforme_id' => $plateforme->id,
                    'externe_id' => $decoded->user_id
                ],
                [
                    'nom' => $decoded->nom,
                    'prenom' => $decoded->prenom,
                    'email' => $decoded->email,
                    'role_id' => Role::where('titre', 'Demandeur')->first()->id, // Rôle par défaut
                    'last_login_at' => now(),
                ]
            );

            // 5. Connexion de l'utilisateur dans la session Laravel
            Auth::login($user);

            // 6. Redirection vers le tableau de bord
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentification échouée : ' . $e->getMessage()], 401);
        }
    }
}
