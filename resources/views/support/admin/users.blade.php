@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Gestion des Utilisateurs</h1>
            <p class="page-subtitle">Administrer tous les utilisateurs du système</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvel utilisateur
            </a>
        </div>
    </div>
</header>

<!-- Filters Section -->
<div class="filters-container" data-testid="filters-container">
    <form method="GET" action="{{ route('admin.users') }}" class="w-full">
        <div class="filters-row">
            <div class="search-bar">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                    <line x1="11" y1="8" x2="11" y2="14"/>
                </svg>
                <input type="text" name="search" class="search-input" placeholder="Rechercher un utilisateur..." value="{{ request('search') }}">
            </div>
            
            <select name="role" class="filter-select">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->titre }}" {{ request('role') == $role->titre ? 'selected' : '' }}>
                        {{ $role->titre }}
                    </option>
                @endforeach
            </select>
            
            <select name="plateforme" class="filter-select">
                <option value="">Toutes les plateformes</option>
                @foreach($plateformes as $plateforme)
                    <option value="{{ $plateforme->id }}" {{ request('plateforme') == $plateforme->id ? 'selected' : '' }}>
                        {{ $plateforme->nom }}
                    </option>
                @endforeach
            </select>
            
            <button type="button" onclick="window.location.href='{{ route('admin.users') }}'" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Effacer
            </button>
            
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="table-container" data-testid="users-table">
    <div class="table">
        <div class="table-header-row">
            <div class="table-header-cell">Utilisateur</div>
            <div class="table-header-cell">Email</div>
            <div class="table-header-cell">Rôle</div>
            <div class="table-header-cell">Plateforme</div>
            <div class="table-header-cell">Statut</div>
            <div class="table-header-cell">Actions</div>
        </div>
        
        @forelse($users as $user)
            <div class="table-row">
                <div class="table-cell">
                    <div class="user-cell">
                        <div class="user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                            {{ strtoupper(substr($user->nom, 0, 1)) }}{{ strtoupper(substr($user->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name">{{ $user->nom }} {{ $user->prenom }}</div>
                            <div class="user-phone">{{ $user->telephone ?? 'Non renseigné' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="table-cell">
                    <a href="mailto:{{ $user->email }}" class="user-email">{{ $user->email }}</a>
                </div>
                
                <div class="table-cell">
                    <span class="badge badge-{{ strtolower(explode(' ', $user->role->titre)[0]) }}">
                        {{ $user->role->titre }}
                    </span>
                </div>
                
                <div class="table-cell">
                    <span class="platform-badge" style="background: {{ $user->plateforme->couleur }};">
                        {{ $user->plateforme->nom }}
                    </span>
                </div>
                
                <div class="table-cell">
                    <span class="badge badge-{{ $user->etat ? 'actif' : 'inactif' }}">
                        {{ $user->etat ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                
                <div class="table-cell">
                    <div class="action-buttons">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline" title="Modifier">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 0-3 3L12 15l-4 1 1-4 6.5-6.5z"/>
                            </svg>
                        </a>
                        
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto text-gray-400 mb-4">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun utilisateur</h3>
                <p class="text-gray-500 mb-4">Aucun utilisateur trouvé pour les critères sélectionnés.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Créer un utilisateur
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($users->hasPages())
    <div class="pagination" data-testid="pagination">
        <div class="pagination-info">
            Affichage de <strong>{{ $users->firstItem() }}</strong> à <strong>{{ $users->lastItem() }}</strong> sur <strong>{{ $users->total() }}</strong> utilisateurs
        </div>
        <div class="pagination-controls">
            {{ $users->links() }}
        </div>
    </div>
@endif

<!-- Styles -->
<style>
.filters-container {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.search-bar {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    z-index: 10;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 3rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    transition: all 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
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

.btn-outline {
    background: none;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.table-container {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table {
    width: 100%;
}

.table-header-row {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1.5fr;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    border-bottom: 1px solid #e5e7eb;
}

.table-row {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1.5fr;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
    align-items: center;
}

.table-row:hover {
    background: #f9fafb;
}

.table-row:last-child {
    border-bottom: none;
}

.table-cell {
    font-size: 0.875rem;
    color: #374151;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.user-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.125rem;
}

.user-phone {
    font-size: 0.75rem;
    color: #6b7280;
}

.user-email {
    color: #2563eb;
    text-decoration: none;
    transition: color 0.2s;
}

.user-email:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
}

.badge-actif { background: #10b981; color: white; }
.badge-inactif { background: #ef4444; color: white; }

.badge-administrateur { background: #8b5cf6; color: white; }
.badge-technicien { background: #3b82f6; color: white; }
.badge-superviseur { background: #f59e0b; color: white; }
.badge-demandeur { background: #6b7280; color: white; }

.platform-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
    text-decoration: none;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 0;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.pagination-controls {
    display: flex;
    align-items: center;
}

@media (max-width: 1024px) {
    .filters-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .table-header-row,
    .table-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .table-header-row > *:not(:first-child),
    .table-row > *:not(:first-child) {
        display: none;
    }
    
    .table-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
    }
    
    .table-cell {
        width: 100%;
        justify-content: flex-start;
    }
    
    .user-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .action-buttons {
        align-self: stretch;
        justify-content: flex-end;
    }
}

@media (max-width: 640px) {
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .pagination {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>
@endsection
