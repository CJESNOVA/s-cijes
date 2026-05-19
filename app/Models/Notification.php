<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $table = 'app_notifications';
    
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphe (ticket, user, etc.)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Marquer la notification comme lue
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Marquer la notification comme non lue
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope pour les notifications lues
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope pour un type de notification spécifique
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtenir l'icône en fonction du type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'ticket_created' => 'plus-circle',
            'ticket_assigned' => 'user-plus',
            'message_added' => 'message-circle',
            'status_changed' => 'activity',
            'ticket_resolved' => 'check-circle',
            'ticket_closed' => 'x-circle',
            'urgent_ticket' => 'alert-triangle',
            'system_maintenance' => 'settings',
            default => 'bell',
        };
    }

    /**
     * Obtenir la couleur en fonction du type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'ticket_created', 'message_added' => 'blue',
            'ticket_assigned' => 'green',
            'status_changed' => 'yellow',
            'ticket_resolved' => 'emerald',
            'ticket_closed' => 'gray',
            'urgent_ticket' => 'red',
            'system_maintenance' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Créer une notification
     */
    public static function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        Model $notifiable = null,
        array $data = []
    ): self {
        return static::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => $notifiable?->getMorphClass(),
            'notifiable_id' => $notifiable?->id,
            'data' => $data,
        ]);
    }
    
    public static function createSimpleNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): self {
        return static::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => null,
            'notifiable_id' => null,
            'data' => $data,
        ]);
    }
}
