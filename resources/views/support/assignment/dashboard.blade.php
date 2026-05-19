@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Assignations</h1>
            <p class="page-subtitle">Gestion des assignations des tickets</p>
        </div>
        <div class="header-right">
            <a href="{{ route('assignment.unassigned') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Tickets non assignés
            </a>
            <a href="{{ route('assignment.stats') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>
</header>

<!-- Stats Grid -->
<div class="stats-grid" data-testid="stats-grid">
    <div class="stat-card" data-testid="stat-unassigned">
        <div class="stat-header">
            <span class="stat-label">Tickets non assignés</span>
            <div class="stat-icon unassigned">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['unassigned_count'] }}</div>
        <div class="stat-change">En attente d'assignation</div>
    </div>

    <div class="stat-card" data-testid="stat-my-tickets">
        <div class="stat-header">
            <span class="stat-label">Mes tickets</span>
            <div class="stat-icon my-tickets">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['my_tickets_count'] }}</div>
        <div class="stat-change">Assignés à vous</div>
    </div>

    <div class="stat-card" data-testid="stat-urgent">
        <div class="stat-header">
            <span class="stat-label">Tickets urgents</span>
            <div class="stat-icon urgent">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01  y2="17"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['urgent_unassigned'] }}</div>
        <div class="stat-change">Urgents non assignés</div>
    </div>

    <div class="stat-card" data-testid="stat-in-progress">
        <div class="stat-header">
            <span class="stat-label">En cours</span>
            <div class="stat-icon in-progress">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 0 0-7H17"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['in_progress'] }}</div>
        <div class="stat-change">En traitement</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <div class="action-card">
        <div class="action-header">
            <h3 class="action-title">Assignation Rapide</h3>
            <p class="action-description">Assigner les tickets non assignés rapidement</p>
        </div>
        <div class="action-content">
            <button class="btn btn-primary" onclick="showAutoAssignModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Assignation automatique
            </button>
        </div>
    </div>
    
    <div class="action-card">
        <div class="action-header">
            <h3 class="action-title">Techniciens Disponibles</h3>
            <p class="action-description">{{ $availableTechnicians->count() }} technicien(s) disponible(s)</p>
        </div>
        <div class="action-content">
            <div class="technician-list">
                @foreach($availableTechnicians->take(5) as $technician)
                    <div class="technician-item">
                        <div class="technician-avatar">
                            {{ substr($technician->prenom, 0, 1) }}{{ substr($technician->nom, 0, 1) }}
                        </div>
                        <div class="technician-info">
                            <div class="technician-name">{{ $technician->nom }} {{ $technician->prenom }}</div>
                            <div class="technician-role">{{ $technician->role->titre }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Tickets Grid -->
<div class="tickets-grid">
    <!-- Unassigned Tickets -->
    <div class="tickets-section">
        <div class="section-header">
            <h3 class="section-title">Tickets non assignés</h3>
            <a href="{{ route('assignment.unassigned') }}" class="btn btn-sm btn-primary">Voir tout</a>
        </div>
        <div class="tickets-list">
            @forelse($unassignedTickets->take(5) as $ticket)
                <div class="ticket-card unassigned">
                    <div class="ticket-header">
                        <div class="ticket-info">
                            <span class="ticket-reference">{{ $ticket->reference }}</span>
                            <span class="badge badge-{{ strtolower(explode(' ', $ticket->priorite->nom)[0]) }}">
                                {{ $ticket->priorite->nom }}
                            </span>
                        </div>
                        <div class="ticket-time">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="ticket-content">
                        <h4 class="ticket-title">{{ $ticket->titre }}</h4>
                        <p class="ticket-description">{{ Str::limit($ticket->description, 100) }}</p>
                        <div class="ticket-meta">
                            <div class="ticket-user">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ $ticket->user->nom }} {{ $ticket->user->prenom }}
                            </div>
                            <div class="ticket-platform">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="9" x2="15" y2="9"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                {{ $ticket->plateforme->nom }}
                            </div>
                        </div>
                    </div>
                    <div class="ticket-actions">
                        <button class="btn btn-sm btn-primary" onclick="showAssignModal({{ $ticket->id }})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Assigner
                        </button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8-11 8-11 8-11 8-11 11 8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Voir
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto text-gray-400 mb-2">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    </svg>
                    <p class="text-gray-500">Aucun ticket non assigné</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- My Tickets -->
    <div class="tickets-section">
        <div class="section-header">
            <h3 class="section-title">Mes tickets</h3>
            <a href="{{ route('my-tickets.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
        </div>
        <div class="tickets-list">
            @forelse($myTickets->take(5) as $ticket)
                <div class="ticket-card assigned">
                    <div class="ticket-header">
                        <div class="ticket-info">
                            <span class="ticket-reference">{{ $ticket->reference }}</span>
                            <span class="badge badge-{{ strtolower(explode(' ', $ticket->priorite->nom)[0]) }}">
                                {{ $ticket->priorite->nom }}
                            </span>
                            <span class="badge badge-{{ strtolower(explode(' ', $ticket->statut->nom)[0]) }}">
                                {{ $ticket->statut->nom }}
                            </span>
                        </div>
                        <div class="ticket-time">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="ticket-content">
                        <h4 class="ticket-title">{{ $ticket->titre }}</h4>
                        <p class="ticket-description">{{ Str::limit($ticket->description, 100) }}</p>
                        <div class="ticket-meta">
                            <div class="ticket-user">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ $ticket->user->nom }} {{ $ticket->user->prenom }}
                            </div>
                            <div class="ticket-platform">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="9" x2="15" y2="9"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                {{ $ticket->plateforme->nom }}
                            </div>
                        </div>
                    </div>
                    <div class="ticket-actions" data-ticket-id="{{ $ticket->id }}" data-technician-id="{{ $ticket->id }}" data-technician-name="{{ $ticket->technicien ? $ticket->technicien->nom . ' ' . $ticket->technicien->prenom : 'null' }}" data-technician-role="{{ $ticket->technicien ? $ticket->technicien->role->titre : 'null' }}">
                        <button class="btn btn-sm btn-primary" onclick="showReassignModal({{ $ticket->id }})" title="Réassigner">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 4v6h6M23 20v-6h-6"/>
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                            </svg>
                            Réassigner
                        </button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8-11 8-11 8-11 8-11 11 8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Traiter
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto text-gray-400 mb-2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <p class="text-gray-500">Aucun ticket assigné</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Auto Assign Modal -->
<div id="autoAssignModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Assignation Automatique</h3>
            <button class="modal-close" onclick="closeAutoAssignModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('assignment.auto-assign') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Stratégie d'assignation</label>
                    <select name="strategy" class="form-select" required>
                        <option value="round_robin">Round Robin (Rotation)</option>
                        <option value="least_loaded">Moins chargé</option>
                        <option value="priority_based">Basé sur la priorité</option>
                    </select>
                    <small class="form-help">Choisissez la stratégie pour assigner les tickets automatiquement</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tickets à assigner</label>
                    <div class="checkbox-group">
                        @foreach($unassignedTickets as $ticket)
                            <label class="checkbox-label">
                                <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="form-checkbox">
                                <span>{{ $ticket->reference }} - {{ $ticket->titre }}</span>
                                <span class="badge badge-{{ strtolower(explode(' ', $ticket->priorite->nom)[0]) }}">
                                    {{ $ticket->priorite->nom }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAutoAssignModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner automatiquement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Assigner un ticket</h3>
            <button class="modal-close" onclick="closeAssignModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('assignment.assign', 0) }}" id="assignForm">
                @csrf
                <input type="hidden" name="ticket_id" id="assignTicketId">
                
                <div class="form-group">
                    <label class="form-label">Technicien</label>
                    <select name="technicien_id" class="form-select" required>
                        <option value="">Sélectionner un technicien</option>
                        @foreach($availableTechnicians as $technician)
                            <option value="{{ $technician->id }}">
                                {{ $technician->nom }} {{ $technician->prenom }} - {{ $technician->role->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Note d'assignation</label>
                    <textarea name="note_assignation" class="form-textarea" rows="3" placeholder="Ajouter une note optionnelle..."></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
<div id="reassignModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Réassigner un ticket</h3>
            <button class="modal-close" onclick="closeReassignModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="" id="reassignForm">
                @csrf
                <input type="hidden" name="ticket_id" id="reassignTicketId">
                
                <div class="form-group">
                    <label class="form-label">Technicien actuel</label>
                    <input type="text" id="currentTechnician" class="form-input" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nouveau technicien</label>
                    <select name="technicien_id" class="form-select" required>
                        <option value="">Sélectionner un technicien</option>
                        @foreach($availableTechnicians as $technician)
                            <option value="{{ $technician->id }}">
                                {{ $technician->nom }} {{ $technician->prenom }} - {{ $technician->role->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Motif de réassignation *</label>
                    <textarea name="motif_reassignation" class="form-textarea" rows="3" placeholder="Expliquez pourquoi ce ticket est réassigné..." required></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeReassignModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Réassigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #3b82f6;
}

.stat-card:nth-child(2) { border-left-color: #10b981; }
.stat-card:nth-child(3) { border-left-color: #ef4444; }
.stat-card:nth-child(4) { border-left-color: #f59e0b; }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.stat-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-icon.unassigned { background: #3b82f6; }
.stat-icon.my-tickets { background: #10b981; }
.stat-icon.urgent { background: #ef4444; }
.stat-icon.in-progress { background: #f59e0b; }

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.875rem;
    color: #6b7280;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.action-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.action-header {
    margin-bottom: 1rem;
}

.action-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.action-description {
    font-size: 0.875rem;
    color: #6b7280;
}

.technician-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.technician-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 0.5rem;
    background: #f9fafb;
}

.technician-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.technician-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
}

.technician-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.tickets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.tickets-section {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.tickets-list {
    padding: 1rem;
}

.ticket-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
}

.ticket-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.ticket-card.unassigned {
    border-left: 4px solid #3b82f6;
}

.ticket-card.assigned {
    border-left: 4px solid #10b981;
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.ticket-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ticket-reference {
    font-weight: 600;
    color: #1f2937;
}

.ticket-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.ticket-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.ticket-description {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
}

.ticket-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.ticket-user,
.ticket-platform {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.ticket-actions {
    display: flex;
    gap: 0.5rem;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
}

.badge-urgent { background: #ef4444; color: white; }
.badge-haute { background: #f59e0b; color: white; }
.badge-normale { background: #10b981; color: white; }
.badge-basse { background: #6b7280; color: white; }

.badge-ouvert { background: #10b981; color: white; }
.badge-en-cours { background: #3b82f6; color: white; }
.badge-résolu { background: #059669; color: white; }
.badge-fermé { background: #6b7280; color: white; }

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

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 0.75rem;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.5rem;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-help {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: background 0.2s;
}

.checkbox-label:hover {
    background: #f3f4f6;
}

.form-checkbox {
    width: 1rem;
    height: 1rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .tickets-grid {
        grid-template-columns: 1fr;
    }
    
    .ticket-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
}
</style>

<script>
function showAutoAssignModal() {
    document.getElementById('autoAssignModal').style.display = 'flex';
}

function closeAutoAssignModal() {
    document.getElementById('autoAssignModal').style.display = 'none';
}

function showAssignModal(ticketId) {
    document.getElementById('assignTicketId').value = ticketId;
    document.getElementById('assignForm').action = '{{ route('assignment.assign', 0) }}'.replace('0', ticketId);
    document.getElementById('assignModal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}

function showReassignModal(ticketId) {
    console.log('Opening reassign modal for ticket:', ticketId);
    
    document.getElementById('reassignTicketId').value = ticketId;
    
    // Construire l'URL correcte
    const baseUrl = window.location.origin;
    const actionUrl = `${baseUrl}/assignment/reassign/${ticketId}`;
    document.getElementById('reassignForm').action = actionUrl;
    
    console.log('Form action set to:', actionUrl);
    
    // Récupérer les informations du ticket depuis les données de la page
    const ticketElement = document.querySelector(`[data-ticket-id="${ticketId}"]`);
    const technicianElement = document.querySelector(`[data-technician-id="${ticketId}"]`);
    
    if (ticketElement && technicianElement) {
        const technicianName = technicianElement.getAttribute('data-technician-name');
        const technicianRole = technicianElement.getAttribute('data-technician-role');
        
        if (technicianName && technicianName !== 'null') {
            document.getElementById('currentTechnician').value = `${technicianName} - ${technicianRole}`;
            console.log('Technician info from page:', technicianName, technicianRole);
        } else {
            document.getElementById('currentTechnician').value = 'Non assigné';
            console.log('No technician assigned');
        }
    } else {
        // Fallback: essayer l'API
        const apiUrl = `${window.location.origin}/api/tickets/${ticketId}`;
        console.log('Fallback: Fetching ticket data from:', apiUrl);
        
        fetch(apiUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Ticket data:', data);
                if (data.technicien) {
                    document.getElementById('currentTechnician').value = 
                        `${data.technicien.nom} ${data.technicien.prenom} - ${data.technicien.role.titre}`;
                } else {
                    document.getElementById('currentTechnician').value = 'Non assigné';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('currentTechnician').value = 'Information non disponible';
            });
    }
    
    document.getElementById('reassignModal').style.display = 'flex';
}

function closeReassignModal() {
    document.getElementById('reassignModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
