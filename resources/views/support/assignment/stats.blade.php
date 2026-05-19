@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Statistiques d'Assignation</h1>
            <p class="page-subtitle">Analyse des performances d'assignation</p>
        </div>
        <div class="header-right">
            <a href="{{ route('assignment.dashboard') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Retour au dashboard
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Imprimer
            </button>
        </div>
    </div>
</header>

<!-- Overview Stats -->
<div class="overview-stats" data-testid="overview-stats">
    <div class="stat-card" data-testid="stat-total">
        <div class="stat-header">
            <span class="stat-label">Total Tickets</span>
            <div class="stat-icon total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_tickets'] }}</div>
        <div class="stat-change">Tous les tickets</div>
    </div>

    <div class="stat-card" data-testid="stat-unassigned">
        <div class="stat-header">
            <span class="stat-label">Non assignés</span>
            <div class="stat-icon unassigned">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['unassigned'] }}</div>
        <div class="stat-change">En attente d'assignation</div>
    </div>

    <div class="stat-card" data-testid="stat-assigned">
        <div class="stat-header">
            <span class="stat-label">Assignés</span>
            <div class="stat-icon assigned">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['assigned'] }}</div>
        <div class="stat-change">Déjà assignés</div>
    </div>

    <div class="stat-card" data-testid="stat-rate">
        <div class="stat-header">
            <span class="stat-label">Taux d'assignation</span>
            <div class="stat-icon rate">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_tickets'] > 0 ? round(($stats['assigned'] / $stats['total_tickets']) * 100, 1) : 0 }}%</div>
        <div class="stat-change">Performance globale</div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Priority Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Tickets non assignés par priorité</h3>
            <div class="chart-actions">
                <button class="btn btn-sm btn-secondary">Export</button>
            </div>
        </div>
        <div class="chart-content">
            <div class="chart-bars">
                @foreach($stats['by_priority'] as $priority => $count)
                    <div class="chart-bar-item">
                        <div class="chart-bar-label">{{ $priority }}</div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill priority-{{ strtolower(explode(' ', $priority)[0]) }}" style="width: {{ ($stats['by_priority']->max() > 0 ? ($count / $stats['by_priority']->max()) * 100 : 0) }}%"></div>
                        </div>
                        <div class="chart-bar-value">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Platform Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Tickets non assignés par plateforme</h3>
            <div class="chart-actions">
                <button class="btn btn-sm btn-secondary">Export</button>
            </div>
        </div>
        <div class="chart-content">
            <div class="chart-bars">
                @foreach($stats['by_platform'] as $platform => $count)
                    <div class="chart-bar-item">
                        <div class="chart-bar-label">{{ $platform }}</div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill platform" style="width: {{ ($stats['by_platform']->max() > 0 ? ($count / $stats['by_platform']->max()) * 100 : 0) }}%"></div>
                        </div>
                        <div class="chart-bar-value">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Technician Load -->
<div class="technician-load-section">
    <div class="section-header">
        <h3 class="section-title">Charge des techniciens</h3>
        <div class="section-info">
            <span class="info-text">{{ $stats['technician_load']->count() }} technicien(s) actif(s)</span>
        </div>
    </div>
    
    <div class="technician-grid">
        @foreach($stats['technician_load'] as $technician)
            <div class="technician-card">
                <div class="technician-header">
                    <div class="technician-avatar">
                        {{ substr($technician->prenom, 0, 1) }}{{ substr($technician->nom, 0, 1) }}
                    </div>
                    <div class="technician-info">
                        <div class="technician-name">{{ $technician->nom }} {{ $technician->prenom }}</div>
                        <div class="technician-role">{{ $technician->role->titre }}</div>
                    </div>
                    <div class="technician-load">
                        <div class="load-number">{{ $technician->tickets_count }}</div>
                        <div class="load-label">tickets</div>
                    </div>
                </div>
                
                <div class="technician-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ min(($technician->tickets_count / 10) * 100, 100) }}%"></div>
                    </div>
                    <div class="progress-text">
                        @if($technician->tickets_count == 0)
                            <span class="status-available">Disponible</span>
                        @elseif($technician->tickets_count <= 5)
                            <span class="status-normal">Charge normale</span>
                        @elseif($technician->tickets_count <= 10)
                            <span class="status-busy">Occupé</span>
                        @else
                            <span class="status-overload">Surcharge</span>
                        @endif
                    </div>
                </div>
                
                <div class="technician-actions">
                    <button class="btn btn-sm btn-outline" onclick="viewTechnicianDetails({{ $technician->id }})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8-11 8-11 8-11 8-11 11 8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        Détails
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Performance Metrics -->
<div class="metrics-section">
    <div class="section-header">
        <h3 class="section-title">Indicateurs de performance</h3>
    </div>
    
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon efficiency">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-title">Efficacité d'assignation</div>
                <div class="metric-value">{{ $stats['total_tickets'] > 0 ? round(($stats['assigned'] / $stats['total_tickets']) * 100, 1) : 0 }}%</div>
                <div class="metric-description">{{ $stats['assigned'] }}/{{ $stats['total_tickets'] }} tickets assignés</div>
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon workload">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 0 0-7H17"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-title">Charge moyenne</div>
                <div class="metric-value">{{ $stats['technician_load']->count() > 0 ? round($stats['technician_load']->sum('tickets_count') / $stats['technician_load']->count(), 1) : 0 }}</div>
                <div class="metric-description">Tickets par technicien</div>
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon backlog">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 0 0-7H17"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-title">Arriéré</div>
                <div class="metric-value">{{ $stats['unassigned'] }}</div>
                <div class="metric-description">Tickets en attente</div>
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon response">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-title">Temps moyen</div>
                <div class="metric-value">2.5h</div>
                <div class="metric-description">Temps d'assignation moyen</div>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.overview-stats {
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

.stat-card:nth-child(2) { border-left-color: #ef4444; }
.stat-card:nth-child(3) { border-left-color: #10b981; }
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

.stat-icon.total { background: #3b82f6; }
.stat-icon.unassigned { background: #ef4444; }
.stat-icon.assigned { background: #10b981; }
.stat-icon.rate { background: #f59e0b; }

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
    min-width: 120px;
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
.chart-bar-fill.platform { background: #8b5cf6; }

.chart-bar-value {
    min-width: 40px;
    text-align: right;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.technician-load-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
}

.info-text {
    font-size: 0.875rem;
    color: #6b7280;
}

.technician-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.technician-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    transition: all 0.2s;
}

.technician-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.technician-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.technician-avatar {
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

.technician-info {
    flex: 1;
}

.technician-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.125rem;
}

.technician-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.technician-load {
    text-align: center;
}

.load-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.load-label {
    font-size: 0.75rem;
    color: #6b7280;
}

.technician-progress {
    margin-bottom: 1rem;
}

.progress-bar {
    height: 8px;
    background: #f3f4f6;
    border-radius: 9999px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 9999px;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.75rem;
    text-align: center;
}

.status-available { color: #10b981; }
.status-normal { color: #3b82f6; }
.status-busy { color: #f59e0b; }
.status-overload { color: #ef4444; }

.technician-actions {
    display: flex;
    gap: 0.5rem;
}

.metrics-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.metric-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.metric-card:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.metric-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.metric-icon.efficiency { background: #10b981; }
.metric-icon.workload { background: #3b82f6; }
.metric-icon.backlog { background: #ef4444; }
.metric-icon.response { background: #f59e0b; }

.metric-content {
    flex: 1;
}

.metric-title {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.125rem;
}

.metric-description {
    font-size: 0.75rem;
    color: #6b7280;
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

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

@media (max-width: 768px) {
    .overview-stats {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .technician-grid {
        grid-template-columns: 1fr;
    }
    
    .metrics-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .section-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
}

@media print {
    .header-right,
    .btn {
        display: none;
    }
    
    .overview-stats,
    .charts-grid,
    .technician-load-section,
    .metrics-section {
        break-inside: avoid;
    }
}
</style>

<script>
function viewTechnicianDetails(technicianId) {
    // Implémenter la vue des détails du technicien
    alert('Détails du technicien ' + technicianId + ' - À implémenter');
}
</script>
@endsection
