@extends('layouts.app')

@section('title', 'Dashboard Équipes - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Dashboard Équipes</h1>
            <p class="page-subtitle">Vue d'ensemble et statistiques des équipes</p>
        </div>
        <div class="header-right">
            <a href="{{ route('teams.index') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour aux équipes
            </a>
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvelle équipe
            </a>
        </div>
    </div>
</header>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_teams'] }}</h3>
                <p class="stat-label">Équipes totales</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10B981;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_members'] }}</h3>
                <p class="stat-label">Membres totaux</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_leaders'] }}</h3>
                <p class="stat-label">Leaders d'équipe</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $teams->count() > 0 ? round($stats['total_members'] / $teams->count(), 1) : 0 }}</h3>
                <p class="stat-label">Membres par équipe (moyenne)</p>
            </div>
        </div>
    </div>

    <!-- Recent Teams and Teams List -->
    <div class="dashboard-grid">
        <!-- Recent Teams -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">Équipes récentes</h2>
                <a href="{{ route('teams.index') }}" class="section-link">Voir tout</a>
            </div>
            
            @if($stats['recent_teams']->count() > 0)
            <div class="recent-teams">
                @foreach($stats['recent_teams'] as $team)
                <div class="recent-team-item" style="border-left: 3px solid {{ $team->color }};">
                    <div class="recent-team-info">
                        <h4 class="recent-team-name">{{ $team->name }}</h4>
                        <p class="recent-team-description">{{ $team->description ?: 'Aucune description' }}</p>
                        <div class="recent-team-meta">
                            <span class="team-supervisor">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ $team->supervisor->nom }} {{ $team->supervisor->prenom }}
                            </span>
                            <span class="team-members">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                {{ $team->member_count }} membre{{ $team->member_count > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                    <div class="recent-team-actions">
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline">
                            Voir
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-section">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h4>Aucune équipe récente</h4>
                <p>Commencez par créer votre première équipe.</p>
                <a href="{{ route('teams.create') }}" class="btn btn-primary">
                    Créer une équipe
                </a>
            </div>
            @endif
        </div>

        <!-- All Teams -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">Toutes les équipes</h2>
                <span class="section-count">{{ $teams->count() }} équipe{{ $teams->count() > 1 ? 's' : '' }}</span>
            </div>
            
            @if($teams->count() > 0)
            <div class="teams-list">
                @foreach($teams as $team)
                <div class="team-list-item" style="border-left: 3px solid {{ $team->color }};">
                    <div class="team-list-info">
                        <h4 class="team-list-name">{{ $team->name }}</h4>
                        <div class="team-list-meta">
                            <span class="team-supervisor">
                                {{ $team->supervisor->nom }} {{ $team->supervisor->prenom }}
                            </span>
                            <span class="team-members-count">{{ $team->member_count }} membres</span>
                            <span class="team-date">Créée le {{ $team->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="team-list-actions">
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
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
            <div class="empty-section">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h4>Aucune équipe</h4>
                <p>Vous n'avez pas encore créé d'équipe.</p>
                <a href="{{ route('teams.create') }}" class="btn btn-primary">
                    Créer une équipe
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0.25rem 0 0 0;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.dashboard-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.section-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.section-link:hover {
    text-decoration: underline;
}

.section-count {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
}

.recent-teams,
.teams-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.recent-team-item,
.team-list-item {
    padding: 1rem;
    border-radius: 8px;
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
}

.recent-team-item:hover,
.team-list-item:hover {
    background: var(--gray-100);
}

.recent-team-info,
.team-list-info {
    flex: 1;
}

.recent-team-name,
.team-list-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.recent-team-description {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
}

.recent-team-meta,
.team-list-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.recent-team-meta span,
.team-list-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.recent-team-actions,
.team-list-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-section {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.empty-section h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.empty-section p {
    color: var(--gray-600);
    margin: 0 0 1.5rem 0;
    font-size: 0.875rem;
}

.pagination-wrapper {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: center;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .recent-team-meta,
    .team-list-meta {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .recent-team-item,
    .team-list-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .recent-team-actions,
    .team-list-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endsection
