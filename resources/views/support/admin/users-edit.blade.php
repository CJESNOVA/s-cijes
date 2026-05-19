@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Modifier l'Utilisateur</h1>
            <p class="page-subtitle">Mettre à jour les informations de l'utilisateur</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>
</header>

<!-- Form Container -->
<div class="form-container" data-testid="user-edit-form">
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Modifier : {{ $user->nom }} {{ $user->prenom }}</h3>
            <p class="form-description">Mettez à jour les informations de l'utilisateur</p>
        </div>
        
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="user-form">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div class="form-section">
                <h4 class="section-title">Informations personnelles</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" id="nom" name="nom" class="form-input" value="{{ old('nom', $user->nom) }}" required>
                        @error('nom')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" class="form-input" value="{{ old('prenom', $user->prenom) }}" required>
                        @error('prenom')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-input" value="{{ old('telephone', $user->telephone) }}">
                        @error('telephone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="externe_id" class="form-label">ID Externe</label>
                        <input type="text" id="externe_id" name="externe_id" class="form-input" value="{{ old('externe_id', $user->externe_id) }}" placeholder="Optionnel">
                        @error('externe_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-help">Identifiant externe optionnel (max 50 caractères)</small>
                    </div>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="form-section">
                <h4 class="section-title">Informations du compte</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Laisser vide pour ne pas changer">
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-help">Minimum 8 caractères. Laissez vide pour conserver l'actuel.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirmer le nouveau mot de passe">
                        @error('password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Role and Platform -->
            <div class="form-section">
                <h4 class="section-title">Rôle et plateforme</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="role_id" class="form-label">Rôle *</label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->titre }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="plateforme_id" class="form-label">Plateforme *</label>
                        <select id="plateforme_id" name="plateforme_id" class="form-select" required>
                            <option value="">Sélectionner une plateforme</option>
                            @foreach($plateformes as $plateforme)
                                <option value="{{ $plateforme->id }}" {{ old('plateforme_id', $user->plateforme_id) == $plateforme->id ? 'selected' : '' }}>
                                    {{ $plateforme->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('plateforme_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="form-section">
                <h4 class="section-title">Statut</h4>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="etat" value="1" {{ old('etat', $user->etat) ? 'checked' : '' }}>
                        <span class="checkbox-text">Compte actif</span>
                    </label>
                    <small class="form-help">L'utilisateur pourra se connecter si cette case est cochée</small>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="form-section">
                <h4 class="section-title">Informations système</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">ID Utilisateur:</span>
                        <span class="info-value">{{ $user->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de création:</span>
                        <span class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dernière mise à jour:</span>
                        <span class="info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Créé par:</span>
                        <span class="info-value">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Styles -->
<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 0;
}

.form-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-header {
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.form-description {
    color: #6b7280;
    font-size: 0.875rem;
}

.user-form {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f3f4f6;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.info-value {
    font-size: 0.875rem;
    color: #111827;
    font-weight: 500;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.5rem;
}

.form-input,
.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.error-message {
    display: block;
    color: #dc2626;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-help {
    display: block;
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
    accent-color: #2563eb;
}

.checkbox-text {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
