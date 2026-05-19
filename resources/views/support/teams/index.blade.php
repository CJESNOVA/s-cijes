@extends('layouts.app')

@section('title', 'Gestion des équipes - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Gestion des équipes</h1>
            <p class="page-subtitle">Organisez et gérez vos équipes de techniciens</p>
        </div>
        <div class="header-right">
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvelle équipe
            </a>
            <a href="{{ route('teams.dashboard') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/>
                    <path d="M3 9h18"/>
                    <path d="M3 15h18"/>
                    <path d="M9 3v18"/>
                    <path d="M15 3v18"/>
                </svg>
                Dashboard
            </a>
        </div>
    </div>
</header>

<!-- Teams List -->
<div class="teams-container">
    @if($teams->count() > 0)
    <div class="teams-grid">
        @foreach($teams as $team)
        <div class="team-card" style="border-left: 4px solid {{ $team->color }};">
            <div class="team-header">
                <h3 class="team-name">{{ $team->name }}</h3>
                <div class="team-badge" style="background-color: {{ $team->color }}20; color: {{ $team->color }};">
                    {{ $team->member_count }} membre{{ $team->member_count > 1 ? 's' : '' }}
                </div>
            </div>
            
            <div class="team-description">
                <p>{{ $team->description ?: 'Aucune description' }}</p>
            </div>
            
            <div class="team-supervisor">
                <strong>Superviseur :</strong> {{ $team->supervisor->nom }} {{ $team->supervisor->prenom }}
            </div>
            
            @if($team->leader)
            <div class="team-leader">
                <strong>Leader d'équipe :</strong> {{ $team->leader->nom }} {{ $team->leader->prenom }}
            </div>
            @endif
            
            <div class="team-actions">
                <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Voir
                </a>
                <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $teams->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <h3>Aucune équipe trouvée</h3>
        <p>Commencez par créer votre première équipe pour organiser vos techniciens.</p>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Créer une équipe
        </a>
    </div>
    @endif
</div>

<!-- Styles -->
<style>
.teams-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.teams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.team-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: all 0.2s;
}

.team-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.team-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.team-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.team-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.team-description {
    margin-bottom: 1rem;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.team-description p {
    margin: 0;
    line-height: 1.5;
}

.team-supervisor,
.team-leader {
    font-size: 0.875rem;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.team-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
}

.empty-icon {
    color: var(--gray-400);
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--gray-600);
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .teams-container {
        padding: 1rem;
    }
    
    .teams-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .team-actions {
        flex-direction: column;
    }
    
    .team-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
