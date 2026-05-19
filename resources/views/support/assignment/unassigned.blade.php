@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Gestion des Tickets</h1>
            <p class="page-subtitle">Liste de tous les tickets avec possibilité d'assignation et réassignation</p>
        </div>
        <div class="header-right">
            <a href="{{ route('assignment.dashboard') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Retour au dashboard
            </a>
            <button class="btn btn-primary" onclick="showAutoAssignModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Assignation automatique
            </button>
        </div>
    </div>
</header>

<!-- Filters Section -->
<div class="filters-container" data-testid="filters-container">
    <form method="GET" action="{{ route('assignment.unassigned') }}" class="w-full">
        <div class="filters-row">
            <div class="search-bar">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" class="search-input" placeholder="Rechercher un ticket..." value="{{ request('search') }}">
            </div>
            
            <select name="priorite" class="filter-select">
                <option value="">Toutes les priorités</option>
                @foreach($priorites as $priorite)
                    <option value="{{ $priorite->id }}" {{ request('priorite') == $priorite->id ? 'selected' : '' }}>
                        {{ $priorite->nom }}
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
            
            <button type="button" onclick="window.location.href='{{ route('assignment.unassigned') }}'" class="btn btn-secondary">
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
<div class="table-container" data-testid="unassigned-tickets">
    <div class="table-header-actions">
        <div class="bulk-actions">
            <input type="checkbox" id="selectAll" class="form-checkbox" onchange="toggleSelectAll()">
            <label for="selectAll" class="checkbox-label">Sélectionner tout</label>
            <button class="btn btn-sm btn-primary" onclick="showBulkAssignModal()" id="bulkAssignBtn" style="display: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Assigner la sélection
            </button>
        </div>
        <div class="table-info">
            <span class="ticket-count">{{ $tickets->total() }} ticket(s) trouvé(s)</span>
        </div>
    </div>
    
    <div class="table">
        <div class="table-header-row">
            <div class="table-header-cell checkbox-cell">
                <input type="checkbox" id="headerSelectAll" class="form-checkbox" onchange="toggleSelectAll()">
            </div>
            <div class="table-header-cell">Référence</div>
            <div class="table-header-cell">Titre</div>
            <div class="table-header-cell">Demandeur</div>
            <div class="table-header-cell">Plateforme</div>
            <div class="table-header-cell">Priorité</div>
            <div class="table-header-cell">Technicien</div>
            <div class="table-header-cell">Date</div>
            <div class="table-header-cell">Actions</div>
        </div>
        
        @forelse($tickets as $ticket)
            <div class="table-row">
                <div class="table-cell checkbox-cell">
                    <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="ticket-checkbox form-checkbox" onchange="updateBulkActions()">
                </div>
                
                <div class="table-cell">
                    <span class="ticket-id">{{ $ticket->reference }}</span>
                </div>
                
                <div class="table-cell">
                    <a href="{{ route('tickets.show', $ticket) }}" class="ticket-title-link">
                        {{ Str::limit($ticket->titre, 80) }}
                    </a>
                </div>
                
                <div class="table-cell">
                    <div class="user-cell">
                        <div class="user-avatar-small" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                            {{ strtoupper(substr($ticket->user->nom, 0, 1)) }}{{ strtoupper(substr($ticket->user->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name">{{ $ticket->user->nom }} {{ $ticket->user->prenom }}</div>
                            <div class="user-email">{{ $ticket->user->email }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="table-cell">
                    <span class="platform-badge" style="background: {{ $ticket->plateforme->couleur }};">
                        {{ $ticket->plateforme->nom }}
                    </span>
                </div>
                
                <div class="table-cell">
                    <span class="badge badge-{{ strtolower(explode(' ', $ticket->priorite->nom)[0]) }}">
                        {{ $ticket->priorite->nom }}
                    </span>
                </div>
                
                <div class="table-cell" data-ticket-id="{{ $ticket->id }}" data-technician-id="{{ $ticket->id }}" data-technician-name="{{ $ticket->technicien ? $ticket->technicien->nom . ' ' . $ticket->technicien->prenom : 'null' }}" data-technician-role="{{ $ticket->technicien ? $ticket->technicien->role->titre : 'null' }}">
                    @if($ticket->technicien)
                        <div class="technician-info">
                            <div class="technician-name-small">{{ $ticket->technicien->nom }} {{ $ticket->technicien->prenom }}</div>
                            <div class="technician-role-small">{{ $ticket->technicien->role->titre }}</div>
                        </div>
                    @else
                        <span class="text-gray-500">Non assigné</span>
                    @endif
                </div>
                
                <div class="table-cell">
                    <div class="date-info">
                        <div class="date-main">{{ $ticket->created_at->format('d/m/Y') }}</div>
                        <div class="date-relative">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                
                <div class="table-cell">
                    <div class="action-buttons">
                        @if($ticket->technicien_id)
                            <button class="btn btn-sm btn-warning" onclick="showReassignModal({{ $ticket->id }})" title="Réassigner">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 4v6h6M23 20v-6h-6"/>
                                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                                </svg>
                                Réassigner
                            </button>
                        @else
                            <button class="btn btn-sm btn-primary" onclick="showAssignModal({{ $ticket->id }})" title="Assigner">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Assigner
                            </button>
                        @endif
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline" title="Voir">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8-11 8-11 8-11 8-11 11 8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Voir
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto text-gray-400 mb-4">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun ticket non assigné</h3>
                <p class="text-gray-500 mb-4">Tous les tickets ont été assignés.</p>
                <a href="{{ route('assignment.dashboard') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Retour au dashboard
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($tickets->hasPages())
    <div class="pagination" data-testid="pagination">
        <div class="pagination-info">
            Affichage de <strong>{{ $tickets->firstItem() }}</strong> à <strong>{{ $tickets->lastItem() }}</strong> sur <strong>{{ $tickets->total() }}</strong> tickets
        </div>
        <div class="pagination-controls">
            {{ $tickets->links() }}
        </div>
    </div>
@endif

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
                        @foreach($tickets as $ticket)
                            <label class="checkbox-label">
                                <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="form-checkbox">
                                <span>{{ $ticket->reference }} - {{ Str::limit($ticket->titre, 50) }}</span>
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
                        @php
                            $technicians = \App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('titre', ['Technicien', 'Administrateur']);
                            })
                            ->where('etat', true)
                            ->get();
                        @endphp
                        @foreach($technicians as $technician)
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

<!-- Bulk Assign Modal -->
<div id="bulkAssignModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Assigner plusieurs tickets</h3>
            <button class="modal-close" onclick="closeBulkAssignModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('assignment.auto-assign') }}" id="bulkAssignForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Technicien</label>
                    <select name="technicien_id" class="form-select" required>
                        <option value="">Sélectionner un technicien</option>
                        @foreach($technicians as $technician)
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
                
                <input type="hidden" name="strategy" value="manual">
                <input type="hidden" name="ticket_ids" id="bulkTicketIds">
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBulkAssignModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner tout</button>
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
                        @foreach($technicians as $technician)
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

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
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

.table-container {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table-header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.bulk-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.table-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.ticket-count {
    font-weight: 500;
}

.table {
    width: 100%;
}

.table-header-row {
    display: grid;
    grid-template-columns: 40px 120px 2fr 1.5fr 1fr 1fr 1fr 1fr 1.5fr;
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
    grid-template-columns: 40px 120px 2fr 1.5fr 1fr 1fr 1fr 1fr 1.5fr;
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
    display: flex;
    align-items: center;
}

.checkbox-cell {
    justify-content: center;
}

.form-checkbox {
    width: 1rem;
    height: 1rem;
    cursor: pointer;
}

.ticket-id {
    font-weight: 600;
    color: #1f2937;
}

.ticket-title-link {
    color: #2563eb;
    text-decoration: none;
    transition: color 0.2s;
}

.ticket-title-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar-small {
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

.user-name {
    font-weight: 500;
    color: #111827;
    font-size: 0.875rem;
}

.user-email {
    font-size: 0.75rem;
    color: #6b7280;
}

.platform-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
    text-decoration: none;
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

.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.date-main {
    font-weight: 500;
    color: #111827;
}

.date-relative {
    font-size: 0.75rem;
    color: #6b7280;
}

.technician-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.technician-name-small {
    font-weight: 500;
    color: #111827;
    font-size: 0.875rem;
}

.technician-role-small {
    font-size: 0.75rem;
    color: #6b7280;
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
    max-width: 600px;
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

.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

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
    max-height: 300px;
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

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
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
    
    .table-header-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .bulk-actions {
        flex-wrap: wrap;
    }
    
    .pagination {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
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

function showBulkAssignModal() {
    const selectedTickets = Array.from(document.querySelectorAll('.ticket-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedTickets.length === 0) {
        alert('Veuillez sélectionner au moins un ticket');
        return;
    }
    
    document.getElementById('bulkTicketIds').value = selectedTickets.join(',');
    document.getElementById('bulkAssignModal').style.display = 'flex';
}

function closeBulkAssignModal() {
    document.getElementById('bulkAssignModal').style.display = 'none';
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox');
    const selectAll = document.getElementById('selectAll') || document.getElementById('headerSelectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.ticket-checkbox:checked').length;
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    
    if (selectedCount > 0) {
        bulkAssignBtn.style.display = 'inline-flex';
    } else {
        bulkAssignBtn.style.display = 'none';
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
