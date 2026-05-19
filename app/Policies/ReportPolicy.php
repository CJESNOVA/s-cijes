<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view reports.
     */
    public function view(User $user): bool
    {
        return in_array($user->role->titre, ['Superviseur', 'Administrateur']);
    }

    /**
     * Determine whether the user can export reports.
     */
    public function export(User $user): bool
    {
        return in_array($user->role->titre, ['Superviseur', 'Administrateur']);
    }

    /**
     * Determine whether the user can access report API.
     */
    public function apiAccess(User $user): bool
    {
        return in_array($user->role->titre, ['Superviseur', 'Administrateur']);
    }
}
