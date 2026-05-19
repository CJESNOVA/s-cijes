@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Administration</h1>
            <p class="page-subtitle">Tableau de bord administrateur</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.users') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Gérer les utilisateurs
            </a>
        </div>
    </div>
</header>

<!-- Stats Grid -->
<div class="stats-grid" data-testid="stats-grid">
    <div class="stat-card" data-testid="stat-users">
        <div class="stat-header">
            <span class="stat-label">Total Utilisateurs</span>
            <div class="stat-icon users">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-change">Tous les utilisateurs</div>
    </div>

    <div class="stat-card" data-testid="stat-tickets">
        <div class="stat-header">
            <span class="stat-label">Total Tickets</span>
            <div class="stat-icon tickets">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_tickets'] }}</div>
        <div class="stat-change">Tous les tickets</div>
    </div>

    <div class="stat-card" data-testid="stat-notifications">
        <div class="stat-header">
            <span class="stat-label">Notifications</span>
            <div class="stat-icon notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_notifications'] }}</div>
        <div class="stat-change">Toutes les notifications</div>
    </div>

    <div class="stat-card" data-testid="stat-plateformes">
        <div class="stat-header">
            <span class="stat-label">Plateformes</span>
            <div class="stat-icon plateformes">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="9" x2="15" y2="9"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_plateformes'] }}</div>
        <div class="stat-change">Toutes les plateformes</div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Tickets Status Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Tickets par Statut</h3>
            <div class="chart-actions">
                <button class="btn btn-sm btn-secondary" onclick="showExportModal('status')"
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        <div class="chart-content">
            <div class="chart-bars">
                @foreach($ticketsParStatut as $statut)
                    <div class="chart-bar-item">
                        <div class="chart-bar-label">{{ $statut->statut }}</div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill" style="width: {{ ($statut->count / $ticketsParStatut->max('count')) * 100 }}%"></div>
                        </div>
                        <div class="chart-bar-value">{{ $statut->count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tickets Priority Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Tickets par Priorité</h3>
            <div class="chart-actions">
                <button class="btn btn-sm btn-secondary" onclick="exportChart('priority')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        <div class="chart-content">
            <div class="chart-bars">
                @foreach($ticketsParPriorite as $priorite)
                    <div class="chart-bar-item">
                        <div class="chart-bar-label">{{ $priorite->priorite }}</div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill priority-{{ strtolower(explode(' ', $priorite->priorite)[0]) }}" style="width: {{ ($priorite->count / $ticketsParPriorite->max('count')) * 100 }}%"></div>
                        </div>
                        <div class="chart-bar-value">{{ $priorite->count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="quick-stats">
    <div class="quick-stat-card urgent">
        <div class="quick-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="quick-stat-content">
            <div class="quick-stat-title">Tickets Urgents</div>
            <div class="quick-stat-value">{{ $stats['urgent_tickets'] }}</div>
        </div>
    </div>

    <div class="quick-stat-card opened">
        <div class="quick-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11H3v10h6v-10z"/>
                <path d="M21 11h-6v10h6v-10z"/>
                <path d="M15 3h-6v6h6v-6z"/>
                <path d="M9 3H3v6h6v-6z"/>
            </svg>
        </div>
        <div class="quick-stat-content">
            <div class="quick-stat-title">Tickets Ouverts</div>
            <div class="quick-stat-value">{{ $stats['tickets_ouverts'] }}</div>
        </div>
    </div>

    <div class="quick-stat-card in-progress">
        <div class="quick-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 0 0-7H17"/>
            </svg>
        </div>
        <div class="quick-stat-content">
            <div class="quick-stat-title">En Cours</div>
            <div class="quick-stat-value">{{ $stats['tickets_en_cours'] }}</div>
        </div>
    </div>

    <div class="quick-stat-card resolved">
        <div class="quick-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="quick-stat-content">
            <div class="quick-stat-title">Résolus</div>
            <div class="quick-stat-value">{{ $stats['tickets_resolus'] }}</div>
        </div>
    </div>
</div>

<!-- Tables Grid -->
<div class="tables-grid">
    <!-- Recent Tickets -->
    <div class="table-card">
        <div class="table-header">
            <h3 class="table-title">Tickets Récents</h3>
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
        </div>
        <div class="table-content">
            <div class="table">
                <div class="table-header-row">
                    <div class="table-header-cell">Référence</div>
                    <div class="table-header-cell">Titre</div>
                    <div class="table-header-cell">Demandeur</div>
                    <div class="table-header-cell">Statut</div>
                    <div class="table-header-cell">Priorité</div>
                </div>
                @foreach($recentTickets as $ticket)
                    <div class="table-row">
                        <div class="table-cell">
                            <span class="ticket-id">{{ $ticket->reference }}</span>
                        </div>
                        <div class="table-cell">
                            <a href="{{ route('tickets.show', $ticket) }}" class="ticket-title-link">
                                {{ Str::limit($ticket->titre, 50) }}
                            </a>
                        </div>
                        <div class="table-cell">
                            <div class="user-cell">
                                <div class="user-avatar-small" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                    {{ strtoupper(substr($ticket->user->nom, 0, 1)) }}{{ strtoupper(substr($ticket->user->prenom, 0, 1)) }}
                                </div>
                                {{ $ticket->user->nom }} {{ $ticket->user->prenom }}
                            </div>
                        </div>
                        <div class="table-cell">
                            <span class="badge badge-{{ strtolower($ticket->statut->nom) }}">
                                {{ $ticket->statut->nom }}
                            </span>
                        </div>
                        <div class="table-cell">
                            <span class="badge badge-{{ strtolower(explode(' ', $ticket->priorite->nom)[0]) }}">
                                {{ $ticket->priorite->nom }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="table-card">
        <div class="table-header">
            <h3 class="table-title">Utilisateurs Récents</h3>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">Voir tout</a>
        </div>
        <div class="table-content">
            <div class="table">
                <div class="table-header-row">
                    <div class="table-header-cell">Utilisateur</div>
                    <div class="table-header-cell">Email</div>
                    <div class="table-header-cell">Rôle</div>
                    <div class="table-header-cell">Plateforme</div>
                    <div class="table-header-cell">Statut</div>
                </div>
                @foreach($recentUsers as $user)
                    <div class="table-row">
                        <div class="table-cell">
                            <div class="user-cell">
                                <div class="user-avatar-small" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                    {{ strtoupper(substr($user->nom, 0, 1)) }}{{ strtoupper(substr($user->prenom, 0, 1)) }}
                                </div>
                                {{ $user->nom }} {{ $user->prenom }}
                            </div>
                        </div>
                        <div class="table-cell">{{ $user->email }}</div>
                        <div class="table-cell">
                            <span class="badge badge-{{ strtolower(explode(' ', $user->role->titre)[0]) }}">
                                {{ $user->role->titre }}
                            </span>
                        </div>
                        <div class="table-cell">{{ $user->plateforme->nom }}</div>
                        <div class="table-cell">
                            <span class="badge badge-{{ $user->etat ? 'actif' : 'inactif' }}">
                                {{ $user->etat ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
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
.stat-card:nth-child(3) { border-left-color: #f59e0b; }
.stat-card:nth-child(4) { border-left-color: #8b5cf6; }

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

.stat-icon.users { background: #3b82f6; }
.stat-icon.tickets { background: #10b981; }
.stat-icon.notifications { background: #f59e0b; }
.stat-icon.plateformes { background: #8b5cf6; }

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

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.chart-content {
    padding: 1rem 0;
}

.chart-bars {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.chart-bar-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.chart-bar-label {
    min-width: 100px;
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.chart-bar {
    flex: 1;
    height: 24px;
    background: #f3f4f6;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}

.chart-bar-fill {
    height: 100%;
    background: #3b82f6;
    border-radius: 9999px;
    transition: width 0.3s ease;
}

.chart-bar-fill.priority-urgent { background: #ef4444; }
.chart-bar-fill.priority-haute { background: #f59e0b; }
.chart-bar-fill.priority-normale { background: #10b981; }
.chart-bar-fill.priority-basse { background: #6b7280; }

.chart-bar-value {
    min-width: 40px;
    text-align: right;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.quick-stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 4px solid #3b82f6;
}

.quick-stat-card.urgent { border-left-color: #ef4444; }
.quick-stat-card.opened { border-left-color: #f59e0b; }
.quick-stat-card.in-progress { border-left-color: #3b82f6; }
.quick-stat-card.resolved { border-left-color: #10b981; }

.quick-stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background: #3b82f6;
}

.quick-stat-card.urgent .quick-stat-icon { background: #ef4444; }
.quick-stat-card.opened .quick-stat-icon { background: #f59e0b; }
.quick-stat-card.in-progress .quick-stat-icon { background: #3b82f6; }
.quick-stat-card.resolved .quick-stat-icon { background: #10b981; }

.quick-stat-content {
    flex: 1;
}

.quick-stat-title {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.quick-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 1.5rem;
}

.table-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.table-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.table-content {
    padding: 0;
}

.table {
    width: 100%;
}

.table-header-row {
    display: grid;
    grid-template-columns: 1fr 2fr 1.5fr 1fr 1fr;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.table-row {
    display: grid;
    grid-template-columns: 1fr 2fr 1.5fr 1fr 1fr;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.table-row:hover {
    background: #f9fafb;
}

.table-cell {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #374151;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
}

.badge-ouvert { background: #10b981; color: white; }
.badge-en-cours { background: #3b82f6; color: white; }
.badge-résolu { background: #059669; color: white; }
.badge-fermé { background: #6b7280; color: white; }

.badge-urgent { background: #ef4444; color: white; }
.badge-haute { background: #f59e0b; color: white; }
.badge-normale { background: #10b981; color: white; }
.badge-basse { background: #6b7280; color: white; }

.badge-administrateur { background: #8b5cf6; color: white; }
.badge-technicien { background: #3b82f6; color: white; }
.badge-superviseur { background: #f59e0b; color: white; }
.badge-demandeur { background: #6b7280; color: white; }

.badge-actif { background: #10b981; color: white; }
.badge-inactif { background: #ef4444; color: white; }

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
    gap: 0.5rem;
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

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-stats {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .tables-grid {
        grid-template-columns: 1fr;
    }
    
    .table-header-row,
    .table-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .table-header-row > *:not(:first-child),
    .table-row > *:not(:first-child) {
        display: none;
    }
    
    .table-row {
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.75rem;
    }
    
    .table-cell {
        justify-content: flex-start;
    }
}
</style>

<script>
// Fonction d'export des graphiques du dashboard admin
function exportChart(type) {
    const startDate = document.getElementById('exportStartDate')?.value || 
        new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    const endDate = document.getElementById('exportEndDate')?.value || 
        new Date().toISOString().split('T')[0];
    
    // Créer un formulaire temporaire pour l'export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/reports/export/tickets';
    form.style.display = 'none';
    
    // Ajouter les champs du formulaire
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    const startDateInput = document.createElement('input');
    startDateInput.type = 'hidden';
    startDateInput.name = 'start_date';
    startDateInput.value = startDate;
    form.appendChild(startDateInput);
    
    const endDateInput = document.createElement('input');
    endDateInput.type = 'hidden';
    endDateInput.name = 'end_date';
    endDateInput.value = endDate;
    form.appendChild(endDateInput);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = 'excel';
    form.appendChild(formatInput);
    
    // Ajouter le formulaire au document et le soumettre
    document.body.appendChild(form);
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '⏳ Exportation...';
    button.disabled = true;
    
    form.submit();
    
    // Restaurer le bouton après un délai
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
        document.body.removeChild(form);
    }, 2000);
}

// Fonction pour afficher le modal de sélection d'export
function showExportModal(chartType) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Exporter les données</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Type d'export</label>
                    <select id="exportFormat" class="form-select">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date de début</label>
                    <input type="date" id="exportStartDate" class="form-input" 
                           value="${new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date de fin</label>
                    <input type="date" id="exportEndDate" class="form-input" 
                           value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="performExport('${chartType}')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Exporter
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Fermer le modal en cliquant à l'extérieur
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Fonction pour effectuer l'export depuis le modal
function performExport(chartType) {
    const format = document.getElementById('exportFormat').value;
    const startDate = document.getElementById('exportStartDate').value;
    const endDate = document.getElementById('exportEndDate').value;
    
    // Créer et soumettre le formulaire d'export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/reports/export/tickets';
    form.style.display = 'none';
    
    // Ajouter les champs nécessaires
    const fields = [
        { name: '_token', value: document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        { name: 'start_date', value: startDate },
        { name: 'end_date', value: endDate },
        { name: 'format', value: format }
    ];
    
    fields.forEach(field => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = field.name;
        input.value = field.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    
    // Fermer le modal
    document.querySelector('.modal').remove();
}

// Améliorer l'expérience utilisateur avec des animations
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des effets de survol sur les cartes
    document.querySelectorAll('.chart-card, .stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            this.style.transition = 'all 0.3s ease';
        });
    });
    
    // Animation des nombres
    document.querySelectorAll('.stat-value').forEach(element => {
        const finalValue = parseInt(element.textContent);
        if (!isNaN(finalValue)) {
            animateValue(element, 0, finalValue, 1500);
        }
    });
});

// Fonction d'animation des nombres
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}
</script>
@endsection
