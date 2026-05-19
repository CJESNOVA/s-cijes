<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'plateforme_id', 
        'externe_id', 'role_id', 'password', 'last_login_at', 'etat'
    ];

    // Relations structurelles
    public function plateforme() { return $this->belongsTo(Plateforme::class); }
    public function role() { return $this->belongsTo(Role::class); }

    // Relations métier
    public function tickets() { return $this->hasMany(Ticket::class, 'user_id'); }
    public function assignations() { return $this->hasMany(Ticket::class, 'technicien_id'); }
    public function messages() { return $this->hasMany(TicketMessage::class); }
    public function notifications() { return $this->hasMany(Notification::class)->latest(); }
    public function unreadNotifications() { return $this->hasMany(Notification::class)->unread()->latest(); }

    /**
     * Route pour les notifications Slack
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.webhook_url');
    }

    /**
     * Vérifie si l'utilisateur a la permission spécifiée
     */
    public function hasPermission($permission)
    {
        // Permissions basées sur les rôles
        $permissions = [
            'access-supervisor-stats' => ['Superviseur', 'Administrateur'],
            'manage-tickets' => ['Technicien', 'Superviseur', 'Administrateur'],
            'view-all-tickets' => ['Technicien', 'Superviseur', 'Administrateur'],
        ];

        $userRole = $this->role->titre;
        
        return isset($permissions[$permission]) && in_array($userRole, $permissions[$permission]);
    }
}
