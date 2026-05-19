<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'active',
        'supervisor_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('active', true);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot(['role', 'joined_at', 'active'])
            ->withTimestamps();
    }

    public function activeUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->wherePivot('active', true)
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Accessors & Mutators
     */
    public function getMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    public function getLeaderAttribute(): ?User
    {
        $leader = $this->members()->where('role', 'leader')->where('active', true)->first();
        return $leader ? $leader->user : null;
    }

    /**
     * Methods
     */
    public function addMember(User $user, string $role = 'member'): TeamMember
    {
        // Vérifier si l'utilisateur est déjà membre
        if ($this->users()->where('user_id', $user->id)->exists()) {
            $member = $this->members()->where('user_id', $user->id)->first();
            $member->update(['active' => true, 'role' => $role]);
            return $member;
        }

        return $this->members()->create([
            'user_id' => $user->id,
            'role' => $role,
            'joined_at' => now(),
            'active' => true,
        ]);
    }

    public function removeMember(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if ($member) {
            return $member->update(['active' => false]);
        }
        return false;
    }

    public function setLeader(User $user): bool
    {
        // Retirer le leader actuel s'il existe
        $this->members()->where('role', 'leader')->update(['role' => 'member']);
        
        // Définir le nouveau leader
        $member = $this->members()->where('user_id', $user->id)->first();
        if ($member) {
            return $member->update(['role' => 'leader']);
        }
        
        return false;
    }

    public function hasMember(User $user): bool
    {
        return $this->activeUsers()->where('user_id', $user->id)->exists();
    }

    public function isLeader(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('role', 'leader')
            ->where('active', true)
            ->exists();
    }
}
