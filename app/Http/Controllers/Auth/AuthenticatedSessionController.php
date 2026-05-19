<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Vérifier si l'utilisateur existe et a un mot de passe (utilisateurs internes)
        $user = \App\Models\User::where('email', $request->email)
            ->whereNotNull('password')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Aucun compte interne trouvé avec cette adresse email.',
            ]);
        }

        // Vérifier si l'utilisateur est un administrateur, technicien ou superviseur
        if (!in_array($user->role->titre, ['Administrateur', 'Technicien', 'Superviseur'])) {
            throw ValidationException::withMessages([
                'email' => 'Seuls les administrateurs, techniciens et superviseurs peuvent se connecter via ce formulaire.',
            ]);
        }

        // Tenter la connexion
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        $request->session()->regenerate();

        // Redirection selon le rôle
        if ($user->role->titre === 'Administrateur') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role->titre === 'Superviseur') {
            return redirect()->intended(route('supervisor.stats'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
