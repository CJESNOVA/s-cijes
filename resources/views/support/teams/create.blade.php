@extends('layouts.app')

@section('title', 'Créer une équipe - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Créer une équipe</h1>
            <p class="page-subtitle">Organisez vos techniciens en équipes efficaces</p>
        </div>
        <div class="header-right">
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
        <form action="{{ route('teams.store') }}" method="POST" class="team-form">
            @csrf
            
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
                        value="{{ old('name') }}"
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
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="supervisor_id" class="form-label">Superviseur *</label>
                    <select id="supervisor_id" name="supervisor_id" class="form-select @error('supervisor_id') form-invalid @enderror" required>
                        <option value="">Sélectionnez un superviseur</option>
                        @foreach($superviseurs as $superviseur)
                        <option value="{{ $superviseur->id }}" {{ old('supervisor_id') == $superviseur->id ? 'selected' : '' }}>
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
                            value="{{ old('color', '#3B82F6') }}"
                        >
                        <input 
                            type="text" 
                            id="color_text" 
                            class="form-text-color-input" 
                            value="{{ old('color', '#3B82F6') }}"
                            placeholder="#3B82F6"
                        >
                    </div>
                    <div class="form-help">Choisissez une couleur pour identifier visuellement l'équipe</div>
                    @error('color')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Team Members -->
            <div class="form-section">
                <h2 class="section-title">Membres de l'équipe</h2>
                
                <div class="form-group">
                    <label class="form-label">Techniciens à inclure</label>
                    <div class="members-grid">
                        @foreach($techniciens as $technicien)
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
                        @endforeach
                    </div>
                    <div class="form-help">Sélectionnez les techniciens et définissez un leader d'équipe</div>
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
                    Créer l'équipe
                </button>
                <a href="{{ route('teams.index') }}" class="btn btn-outline">
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

@media (max-width: 768px) {
    .team-form-container {
        padding: 1rem;
    }
    
    .form-section {
        padding: 1.5rem;
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
</script>
@endsection
