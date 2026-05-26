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
    $token = $request->query('token');

    if (!$token) {
        return response()->json(['error' => 'Token manquant'], 400);
    }

    try {

        // Décodage NON sécurisé juste pour identifier la plateforme
        $payloadUnsafe = json_decode(
            base64_decode(strtr(explode('.', $token)[1], '-_', '+/'))
        );

        if (!isset($payloadUnsafe->plateforme_code)) {
            return response()->json(['error' => 'Token invalide'], 400);
        }

        $plateforme = Plateforme::where('code', $payloadUnsafe->plateforme_code)->first();

        if (!$plateforme) {
            return response()->json(['error' => 'Plateforme inconnue'], 404);
        }

        // Vérification signature
        $decoded = JWT::decode(
            $token,
            new Key($plateforme->secret_key, 'HS256')
        );
//dd($decoded);
        $role = Role::where('titre', 'Demandeur')->first();

        if (!$role) {
            return response()->json(['error' => 'Rôle introuvable'], 500);
        }

        $user = User::updateOrCreate(
            [
                'plateforme_id' => $plateforme->id,
                'email' => $decoded->email
            ],
            [
                'nom' => $decoded->nom,
                'prenom' => $decoded->prenom,
                'role_id' => $role->id,
                'externe_id' => $decoded->user_id || 0,
                'last_login_at' => now(),
            ]
        );

        Auth::login($user, true);

        return redirect()->route('dashboard');

    } catch (\Firebase\JWT\ExpiredException $e) {
        return response()->json(['error' => 'Token expiré'], 401);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Authentification échouée : ' . $e->getMessage()], 401);
    }
}
}
