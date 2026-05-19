@extends('layouts.app')

@section('title', 'Modifier une équipe - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Modifier une équipe</h1>
            <p class="page-subtitle">Mettez à jour les informations et les membres de l'équipe</p>
        </div>
        <div class="header-right">
            <a href="{{ route('teams.show', $team) }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                Voir l'équipe
            </a>
            <a href="{{ route('teams.index') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</header>

<!-- Form Container -->
<div class="team-form-container">
    <div class="team-form-card">
        <form action="{{ route('teams.update', $team) }}" method="POST" class="team-form">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h2 class="section-title">Informations de base</h2>
                
                <div class="form-group">
                    <label for="name" class="form-label">Nom de l'équipe *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input @error('name') form-invalid @enderror" 
                        value="{{ old('name', $team->name) }}"
                        placeholder="Entrez un nom clair pour l'équipe"
                        required
                    >
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea @error('description') form-invalid @enderror" 
                        rows="3"
                        placeholder="Décrivez l'objectif ou la spécialité de cette équipe"
                    >{{ old('description', $team->description) }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="supervisor_id" class="form-label">Superviseur *</label>
                    <select id="supervisor_id" name="supervisor_id" class="form-select @error('supervisor_id') form-invalid @enderror" required>
                        <option value="">Sélectionnez un superviseur</option>
                        @foreach($superviseurs as $superviseur)
                        <option value="{{ $superviseur->id }}" {{ old('supervisor_id', $team->supervisor_id) == $superviseur->id ? 'selected' : '' }}>
                            {{ $superviseur->nom }} {{ $superviseur->prenom }}
                        </option>
                        @endforeach
                    </select>
                    @error('supervisor_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="color" class="form-label">Couleur de l'équipe</label>
                    <div class="color-input-group">
                        <input 
                            type="color" 
                            id="color" 
                            name="color" 
                            class="form-color-input @error('color') form-invalid @enderror" 
                            value="{{ old('color', $team->color) }}"
                        >
                        <input 
                            type="text" 
                            id="color_text" 
                            class="form-text-color-input" 
                            value="{{ old('color', $team->color) }}"
                            placeholder="#3B82F6"
                        >
                    </div>
                    <div class="form-help">Choisissez une couleur pour identifier visuellement l'équipe</div>
                    @error('color')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Current Members -->
            <div class="form-section">
                <h2 class="section-title">Membres actuels de l'équipe</h2>
                
                @if($team->activeMembers->count() > 0)
                <div class="current-members">
                    @foreach($team->activeMembers as $member)
                    <div class="current-member-item" style="border-left: 4px solid {{ $member->role_color }};">
                        <div class="member-avatar">
                            <div class="user-avatar-medium" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px;">
                                {{ strtoupper(substr($member->user->nom, 0, 1)) }}{{ strtoupper(substr($member->user->prenom, 0, 1)) }}
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">{{ $member->user->nom }} {{ $member->user->prenom }}</h4>
                            <div class="member-details">
                                <span class="member-role-badge" style="background-color: {{ $member->role_color }}20; color: {{ $member->role_color }};">
                                    {{ $member->role_label }}
                                </span>
                                <span class="member-date">Depuis le {{ $member->joined_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="member-actions">
                            <button type="button" onclick="removeMember({{ $team->id }}, {{ $member->user_id }})" class="btn btn-sm btn-danger">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3,6 5,6 21,6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Retirer
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-current-members">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h4>Aucun membre dans cette équipe</h4>
                    <p>Ajoutez des techniciens ci-dessous pour compléter cette équipe.</p>
                </div>
                @endif
            </div>
            
            <!-- Add Members -->
            <div class="form-section">
                <h2 class="section-title">Ajouter des membres</h2>
                
                <div class="form-group">
                    <label class="form-label">Techniciens disponibles</label>
                    <div class="members-grid">
                        @foreach($techniciens as $technicien)
                        @if(!$team->hasMember($technicien))
                        <div class="member-item">
                            <label class="member-checkbox">
                                <input 
                                    type="checkbox" 
                                    name="members[]" 
                                    value="{{ $technicien->id }}"
                                    class="member-checkbox-input"
                                    {{ old('members') && in_array($technicien->id, old('members')) ? 'checked' : '' }}
                                >
                                <div class="member-info">
                                    <div class="member-name">{{ $technicien->nom }} {{ $technicien->prenom }}</div>
                                    <div class="member-role">Technicien</div>
                                </div>
                            </label>
                            <div class="member-role-select">
                                <select name="leader_id" class="leader-select" data-member-id="{{ $technicien->id }}">
                                    <option value="">Membre</option>
                                    <option value="{{ $technicien->id }}" {{ old('leader_id') == $technicien->id ? 'selected' : '' }}>
                                        Leader d'équipe
                                    </option>
                                </select>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @if($techniciens->whereNotIn('id', $team->activeMembers->pluck('user_id'))->count() === 0)
                    <div class="form-help">Tous les techniciens sont déjà membres de cette équipe</div>
                    @else
                    <div class="form-help">Sélectionnez les techniciens à ajouter et définissez un leader d'équipe</div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Mettre à jour l'équipe
                </button>
                <a href="{{ route('teams.show', $team) }}" class="btn btn-outline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Styles -->
<style>
.team-form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.team-form-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.form-section {
    padding: 2rem;
    border-bottom: 1px solid var(--gray-200);
}

.form-section:last-child {
    border-bottom: none;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--gray-900);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-input.form-invalid,
.form-select.form-invalid,
.form-textarea.form-invalid {
    border-color: var(--danger);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-help {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.form-error {
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.color-input-group {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.form-color-input {
    width: 60px;
    height: 40px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.form-color-input:focus {
    outline: none;
    border-color: var(--primary);
}

.form-text-color-input {
    flex: 1;
    max-width: 150px;
}

.current-members {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.current-member-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    background: var(--gray-50);
    transition: all 0.2s;
}

.current-member-item:hover {
    background: var(--gray-100);
}

.member-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.member-info {
    flex: 1;
}

.member-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.member-details {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.member-role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.member-date {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.member-actions {
    flex-shrink: 0;
}

.empty-current-members {
    text-align: center;
    padding: 2rem 1rem;
    background: var(--gray-50);
    border-radius: 8px;
}

.empty-icon {
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.empty-current-members h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.empty-current-members p {
    color: var(--gray-600);
    margin: 0;
    font-size: 0.875rem;
}

.members-grid {
    display: grid;
    gap: 1rem;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    transition: all 0.2s;
}

.member-item:hover {
    border-color: var(--gray-300);
    background: var(--gray-50);
}

.member-checkbox {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    flex: 1;
}

.member-checkbox-input {
    width: 20px;
    height: 20px;
    accent-color: var(--primary);
}

.member-info {
    flex: 1;
}

.member-name {
    font-weight: 500;
    color: var(--gray-900);
}

.member-role {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.member-role-select {
    flex-shrink: 0;
}

.leader-select {
    padding: 0.5rem;
    border: 2px solid var(--gray-200);
    border-radius: 6px;
    font-size: 0.75rem;
    background: white;
}

.form-actions {
    padding: 2rem;
    background: var(--gray-50);
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn-danger {
    background: var(--danger);
    color: white;
    border: 1px solid var(--danger);
}

.btn-danger:hover {
    background: #dc2626;
    border-color: #dc2626;
}

@media (max-width: 768px) {
    .team-form-container {
        padding: 1rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .current-member-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .member-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    .member-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .member-checkbox {
        width: 100%;
    }
    
    .member-role-select {
        width: 100%;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorTextInput = document.getElementById('color_text');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox-input');
    const leaderSelects = document.querySelectorAll('.leader-select');
    
    // Synchronisation des inputs de couleur
    colorInput.addEventListener('input', function() {
        colorTextInput.value = this.value;
    });
    
    colorTextInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorInput.value = this.value;
        }
    });
    
    // Gestion des leaders
    memberCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const memberId = this.value;
            const leaderSelect = document.querySelector(`.leader-select[data-member-id="${memberId}"]`);
            
            if (!this.checked) {
                leaderSelect.value = '';
                leaderSelect.disabled = true;
            } else {
                leaderSelect.disabled = false;
            }
        });
    });
    
    // Initialiser l'état des selects de leader
    leaderSelects.forEach(select => {
        const memberId = select.dataset.memberId;
        const checkbox = document.querySelector(`.member-checkbox-input[value="${memberId}"]`);
        
        if (!checkbox.checked) {
            select.disabled = true;
        }
    });
});

function removeMember(teamId, userId) {
    if (confirm('Êtes-vous sûr de vouloir retirer ce membre de l\'équipe ?')) {
        fetch(`/teams/${teamId}/members/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
@endsection
