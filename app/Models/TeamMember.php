<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'joined_at',
        'active',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeLeader($query)
    {
        return $query->where('role', 'leader');
    }

    public function scopeMember($query)
    {
        return $query->where('role', 'member');
    }

    /**
     * Accessors & Mutators
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'leader' => 'Chef d\'équipe',
            'member' => 'Membre',
            default => 'Inconnu',
        };
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'leader' => '#10B981', // Vert
            'member' => '#3B82F6', // Bleu
            default => '#6B7280', // Gris
        };
    }

    /**
     * Methods
     */
    public function promoteToLeader(): bool
    {
        return $this->update(['role' => 'leader']);
    }

    public function demoteToMember(): bool
    {
        return $this->update(['role' => 'member']);
    }

    public function deactivate(): bool
    {
        return $this->update(['active' => false]);
    }

    public function reactivate(): bool
    {
        return $this->update(['active' => true]);
    }

    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
