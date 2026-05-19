@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Mes Tickets</h1>
            <p class="page-subtitle">Suivez et gérez vos demandes de support</p>
        </div>
        <div class="header-right">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary" data-testid="create-ticket-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouveau Ticket
            </a>
            <button class="icon-button" data-testid="notifications-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notification-badge">3</span>
            </button>
        </div>
    </div>
</header>

<!-- Stats Cards -->
<div class="stats-grid" data-testid="stats-grid">
    <div class="stat-card" data-testid="stat-total-tickets">
        <div class="stat-header">
            <span class="stat-label">Total Tickets</span>
            <div class="stat-icon total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-change">Toutes vos demandes</div>
    </div>

    <div class="stat-card" data-testid="stat-open-tickets">
        <div class="stat-header">
            <span class="stat-label">Tickets Ouverts</span>
            <div class="stat-icon open">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['ouverts'] }}</div>
        <div class="stat-change">En attente de traitement</div>
    </div>

    <div class="stat-card" data-testid="stat-progress-tickets">
        <div class="stat-header">
            <span class="stat-label">En Cours</span>
            <div class="stat-icon progress">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['en_cours'] }}</div>
        <div class="stat-change">En cours de traitement</div>
    </div>

    <div class="stat-card" data-testid="stat-resolved-tickets">
        <div class="stat-header">
            <span class="stat-label">Résolus</span>
            <div class="stat-icon resolved">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['resolus'] }}</div>
        <div class="stat-change">Demandes résolues</div>
    </div>
</div>

<!-- Filters Section -->
<div class="filters-container" data-testid="filters-container">
    <form method="GET" action="{{ route('my-tickets.index') }}" class="w-full">
        <div class="search-bar">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" name="search" class="search-input" placeholder="Rechercher un ticket..." value="{{ request('search') }}" data-testid="search-input">
        </div>
        
        <div class="filters-row">
            <select name="plateforme_id" class="filter-select" data-testid="filter-platform">
                <option value="">Toutes les plateformes</option>
                @foreach($filterData['plateformes'] as $plateforme)
                    <option value="{{ $plateforme->id }}" {{ request('plateforme_id') == $plateforme->id ? 'selected' : '' }}>{{ $plateforme->nom }}</option>
                @endforeach
            </select>
            
            <select name="statut_id" class="filter-select" data-testid="filter-status">
                <option value="">Tous les statuts</option>
                @foreach($filterData['statuts'] as $statut)
                    <option value="{{ $statut->id }}" {{ request('statut_id') == $statut->id ? 'selected' : '' }}>{{ $statut->nom }}</option>
                @endforeach
            </select>
            
            <select name="priorite_id" class="filter-select" data-testid="filter-priority">
                <option value="">Toutes les priorités</option>
                @foreach($filterData['priorites'] as $priorite)
                    <option value="{{ $priorite->id }}" {{ request('priorite_id') == $priorite->id ? 'selected' : '' }}>{{ $priorite->nom }}</option>
                @endforeach
            </select>
            
            <button type="button" onclick="window.location.href='{{ route('my-tickets.index') }}'" class="btn btn-secondary" data-testid="clear-filters">
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

<!-- Tickets Table -->
<div class="table-container" data-testid="tickets-table-container">
    <table class="tickets-table">
        <thead>
            <tr>
                <th data-testid="th-id">Référence</th>
                <th data-testid="th-title">Titre</th>
                <th data-testid="th-platform">Plateforme</th>
                <th data-testid="th-priority">Priorité</th>
                <th data-testid="th-status">Statut</th>
                <th data-testid="th-assigned">Assigné à</th>
                <th data-testid="th-date">Date</th>
                <th data-testid="th-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr class="table-row" data-testid="ticket-row">
                <td class="ticket-id">#{{ $ticket->reference }}</td>
                <td class="ticket-title">
                    <a href="{{ route('tickets.show', $ticket) }}" data-testid="ticket-link">{{ $ticket->titre }}</a>
                </td>
                <td>
                    <span class="badge badge-platform" data-testid="platform-badge">
                        <span class="platform-dot" style="background: #2563EB;"></span>
                        {{ $ticket->plateforme->nom }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $ticket->priorite->niveau == 3 ? 'urgent' : ($ticket->priorite->niveau == 2 ? 'high' : ($ticket->priorite->niveau == 1 ? 'normal' : 'low')) }}" data-testid="priority-badge">
                        {{ $ticket->priorite->nom }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $ticket->statut->code }}" data-testid="status-badge">
                        {{ $ticket->statut->nom }}
                    </span>
                </td>
                <td class="ticket-assigned">
                    @if($ticket->technicien)
                        <div class="user-cell">
                            <div class="user-avatar-small" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                {{ strtoupper(substr($ticket->technicien->nom, 0, 1)) }}{{ strtoupper(substr($ticket->technicien->prenom, 0, 1)) }}
                            </div>
                            <span>{{ $ticket->technicien->nom }} {{ $ticket->technicien->prenom }}</span>
                        </div>
                    @else
                        <span class="unassigned-text">Non assigné</span>
                    @endif
                </td>
                <td class="ticket-date">{{ $ticket->created_at->diffForHumans() }}</td>
                <td>
                    <a href="{{ route('tickets.show', $ticket) }}" class="action-button" data-testid="view-ticket-button" title="Voir le ticket">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-8 text-gray-500">
                    Aucun ticket trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

<!-- Pagination -->
<div class="pagination" data-testid="pagination">
    <div class="pagination-info">
        Affichage de <strong>{{ $tickets->firstItem() }}</strong> à <strong>{{ $tickets->lastItem() }}</strong> sur <strong>{{ $tickets->total() }}</strong> tickets
    </div>
    <div class="pagination-controls">
        {{ $tickets->links() }}
    </div>
</div>
</div>

<!-- Styles supplémentaires pour la liste -->
<style>
.badge-platform {
    background: #f3f4f6;
    color: #374151;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.platform-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.badge-urgent {
    background: #fee2e2;
    color: #dc2626;
}

.badge-high {
    background: #fed7aa;
    color: #ea580c;
}

.badge-normal {
    background: #fef3c7;
    color: #d97706;
}

.badge-low {
    background: #dbeafe;
    color: #2563eb;
}

.badge-ouvert {
    background: #dbeafe;
    color: #2563eb;
}

.badge-en-cours {
    background: #fef3c7;
    color: #d97706;
}

.badge-resolu {
    background: #d1fae5;
    color: #059669;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
}

.unassigned-text {
    color: #9ca3af;
    font-style: italic;
}

.action-button {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.action-button:hover {
    background: #f3f4f6;
    color: #374151;
}

.filters-container {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.search-bar {
    position: relative;
    margin-bottom: 1rem;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    z-index: 10;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-select {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
}

.filter-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.table-container {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tickets-table {
    width: 100%;
    border-collapse: collapse;
}

.tickets-table th {
    background: #f9fafb;
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    border-bottom: 1px solid #e5e7eb;
}

.tickets-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
}

.table-row:hover {
    background: #f9fafb;
}

.ticket-id {
    font-weight: 600;
    color: #374151;
}

.ticket-title a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}

.ticket-title a:hover {
    text-decoration: underline;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
}

/* Styles pour la pagination Laravel */
.pagination-controls .pagination {
    display: flex;
    gap: 0.25rem;
    margin: 0;
}

.pagination-controls .pagination span {
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    color: #6b7280;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.pagination-controls .pagination a {
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    color: #374151;
    background: white;
    border: 1px solid #d1d5db;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-controls .pagination a:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination-controls .pagination .page-item.active .page-link {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}
</style>
@endsection
