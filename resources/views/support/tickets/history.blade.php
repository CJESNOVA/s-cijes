@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Historique - {{ $ticket->reference }}</h1>
            <p class="page-subtitle">Historique détaillé de toutes les activités du ticket</p>
        </div>
        <div class="header-right">
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary" data-testid="back-to-ticket-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 14 4 9 9 4"/>
                    <path d="M20 20v-7a4 4 0 0 0-4-4H4"/>
                </svg>
                Retour au ticket
            </a>
            <button class="btn btn-primary" onclick="refreshHistory()" data-testid="refresh-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 4v6h-6"/>
                    <path d="M1 20v-6h6"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                Actualiser
            </button>
        </div>
    </div>
</header>

<!-- Ticket Info Summary -->
<div class="ticket-summary-card" data-testid="ticket-summary">
    <div class="summary-header">
        <h2>{{ $ticket->titre }}</h2>
        <div class="ticket-meta">
            <span class="meta-item">
                <strong>Référence:</strong> {{ $ticket->reference }}
            </span>
            <span class="meta-item">
                <strong>Demandeur:</strong> {{ $ticket->user->nom }} {{ $ticket->user->prenom }}
            </span>
            <span class="meta-item">
                <strong>Statut:</strong> 
                <span class="badge badge-{{ strtolower(str_replace(' ', '-', $ticket->statut->nom)) }}">
                    {{ $ticket->statut->nom }}
                </span>
            </span>
            <span class="meta-item">
                <strong>Priorité:</strong> {{ $ticket->priorite->nom }}
            </span>
            @if($ticket->technicien)
            <span class="meta-item">
                <strong>Assigné à:</strong> {{ $ticket->technicien->nom }} {{ $ticket->technicien->prenom }}
            </span>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid" data-testid="history-stats">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total des événements</span>
            <div class="stat-icon total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_events'] }}</div>
        <div class="stat-change">Toutes les activités</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Changements de statut</span>
            <div class="stat-icon status">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['status_changes'] }}</div>
        <div class="stat-change">Modifications de statut</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Changements de priorité</span>
            <div class="stat-icon priority">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['priority_changes'] }}</div>
        <div class="stat-change">Modifications de priorité</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Assignations</span>
            <div class="stat-icon assignment">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <path d="M20 8v6"/>
                    <path d="M23 11h-6"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['assignments'] }}</div>
        <div class="stat-change">Réassignations</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Messages</span>
            <div class="stat-icon messages">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['messages'] }}</div>
        <div class="stat-change">Réponses échangées</div>
    </div>
</div>

<!-- History Timeline -->
<div class="history-container" data-testid="history-container">
    <div class="history-header">
        <h2>Chronologie des événements</h2>
        <div class="history-filters">
            <select id="event-filter" class="filter-select" onchange="filterHistory()">
                <option value="">Tous les événements</option>
                <option value="statut">Changements de statut</option>
                <option value="priorité">Changements de priorité</option>
                <option value="assignation">Assignations</option>
                <option value="message">Messages</option>
                <option value="fichier">Fichiers</option>
                <option value="satisfaction">Satisfaction</option>
            </select>
        </div>
    </div>

    <div class="history-timeline" id="history-timeline">
        @forelse($historiques as $historique)
            <div class="timeline-item" data-event-type="{{ $historique->getEventType() }}" data-testid="history-item-{{ $historique->id }}">
                <div class="timeline-dot {{ $historique->getEventColor() }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        {{ $historique->getEventIcon() }}
                    </svg>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div class="timeline-title">{{ $historique->getFormattedAction() }}</div>
                        <div class="timeline-meta">
                            <span class="timeline-user">{{ $historique->user->nom }} {{ $historique->user->prenom }}</span>
                            <span class="timeline-time">{{ $historique->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @if($historique->details)
                        <div class="timeline-details">
                            {{ $historique->getFormattedDetails() }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-history">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <h3>Aucun historique</h3>
                <p>Ce ticket n'a pas encore d'historique d'événements.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($historiques->hasPages())
        <div class="pagination-wrapper">
            {{ $historiques->links() }}
        </div>
    @endif
</div>

<!-- Styles -->
<style>
.header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.ticket-summary-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.summary-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

.ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.meta-item {
    font-size: 0.875rem;
    color: #6b7280;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-icon.total { background: #3b82f6; }
.stat-icon.status { background: #10b981; }
.stat-icon.priority { background: #f59e0b; }
.stat-icon.assignment { background: #8b5cf6; }
.stat-icon.messages { background: #ef4444; }

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    color: #6b7280;
}

.history-container {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.history-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
}

.history-timeline {
    position: relative;
    padding-left: 2rem;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.timeline-dot {
    position: absolute;
    left: -2.25rem;
    top: 0.25rem;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.timeline-dot.status { background: #10b981; }
.timeline-dot.priority { background: #f59e0b; }
.timeline-dot.assignment { background: #8b5cf6; }
.timeline-dot.message { background: #3b82f6; }
.timeline-dot.file { background: #ef4444; }
.timeline-dot.satisfaction { background: #ec4899; }
.timeline-dot.default { background: #6b7280; }

.timeline-content {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-left: 0.5rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.timeline-title {
    font-weight: 600;
    color: #1f2937;
}

.timeline-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.timeline-details {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
    font-size: 0.875rem;
    color: #6b7280;
}

.empty-history {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.empty-history svg {
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-history h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #4b5563;
}

.pagination-wrapper {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-ouvert { background: #dbeafe; color: #2563eb; }
.badge-en-cours { background: #fef3c7; color: #d97706; }
.badge-résolu { background: #d1fae5; color: #059669; }
.badge-fermé { background: #f3f4f6; color: #6b7280; }
</style>

<!-- JavaScript -->
<script>
function refreshHistory() {
    window.location.reload();
}

function filterHistory() {
    const filter = document.getElementById('event-filter').value;
    const items = document.querySelectorAll('.timeline-item');
    
    items.forEach(item => {
        if (!filter || item.dataset.eventType === filter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Auto-refresh toutes les 30 secondes
setInterval(() => {
    fetch(`/api/tickets/{{ $ticket->id }}/history`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour l'interface si de nouveaux événements sont détectés
            console.log('History refreshed:', data.historiques.length);
        })
        .catch(error => console.error('Error refreshing history:', error));
}, 30000);
</script>
@endsection
