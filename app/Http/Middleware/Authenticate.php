<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si c'est une requête API, ne pas rediriger
        if ($request->expectsJson()) {
            return null;
        }

        // Si c'est une route SSO, ne pas rediriger vers login
        if ($request->is('sso/*')) {
            return null;
        }

        // Rediriger vers le formulaire de connexion pour les utilisateurs internes
        return route('login');
    }
}
