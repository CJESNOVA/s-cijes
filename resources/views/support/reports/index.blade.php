@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Rapports Exportables</h1>
            <p class="page-subtitle">Analyse et exportation des données du support</p>
        </div>
        <div class="header-right">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</header>

<!-- Date Filter -->
<div class="filter-section">
    <div class="filter-card">
        <h3 class="filter-title">Période d'analyse</h3>
        <form method="GET" action="{{ route('reports.index') }}" class="filter-form">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">Date de début</label>
                    <input type="date" name="start_date" class="form-input" value="{{ $startDate }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="end_date" class="form-input" value="{{ $endDate }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Overview -->
<div class="stats-overview">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_tickets'] }}</div>
                <div class="stat-label">Total Tickets</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['tickets_resolus'] }}</div>
                <div class="stat-label">Tickets Résolus</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['tickets_en_cours'] }}</div>
                <div class="stat-label">En Cours</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11H3v2h6v-2zm0-4H3v2h6V7zm0 8H3v2h6v-2zm12-8h-6v2h6V7zm0 4h-6v2h6v-2zm0 4h-6v2h6v-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['tickets_non_assignes'] }}</div>
                <div class="stat-label">Non Assignés</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['temps_moyen_resolution'] }}</div>
                <div class="stat-label">Temps Moyen</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 20V10"/>
                    <path d="M12 20V4"/>
                    <path d="M6 20v-6"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['taux_resolution'] }}%</div>
                <div class="stat-label">Taux de Résolution</div>
            </div>
        </div>
    </div>
</div>

<!-- Export Section -->
<div class="export-section">
    <div class="export-header">
        <h2 class="section-title">Exportations Disponibles</h2>
        <p class="section-description">Téléchargez les rapports détaillés dans différents formats</p>
    </div>
    
    <div class="export-grid">
        <!-- Tickets Export -->
        <div class="export-card">
            <div class="export-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div class="export-content">
                <h3 class="export-title">Tickets Complet</h3>
                <p class="export-description">Exportez tous les tickets avec leurs détails, statuts et techniciens assignés</p>
                <div class="export-actions">
                    <form method="POST" action="{{ route('reports.export.tickets') }}" class="export-form">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <div class="export-buttons">
                            <button type="submit" name="format" value="excel" class="btn btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Excel
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-outline">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Performance Export -->
        <div class="export-card">
            <div class="export-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9v-2H5.5l8.5-8.5z"/>
                    <path d="M16 22l2-6h-3l-2 6z"/>
                    <path d="M20 22l2-6h-3l-2 6z"/>
                </svg>
            </div>
            <div class="export-content">
                <h3 class="export-title">Performance Techniciens</h3>
                <p class="export-description">Analyse détaillée de la performance de chaque technicien avec scores et métriques</p>
                <div class="export-actions">
                    <form method="POST" action="{{ route('reports.export.performance') }}" class="export-form">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <div class="export-buttons">
                            <button type="submit" name="format" value="excel" class="btn btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Excel
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-outline">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Technicians Export -->
        <div class="export-card">
            <div class="export-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="export-content">
                <h3 class="export-title">Charge des Techniciens</h3>
                <p class="export-description">Répartition de la charge de travail et statistiques par technicien</p>
                <div class="export-actions">
                    <form method="POST" action="{{ route('reports.export.techniciens') }}" class="export-form">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <div class="export-buttons">
                            <button type="submit" name="format" value="excel" class="btn btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Excel
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-outline">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="charts-header">
        <h2 class="section-title">Visualisations</h2>
        <p class="section-description">Graphiques interactifs pour analyser les tendances</p>
    </div>
    
    <div class="charts-grid">
        <!-- Tickets per day chart -->
        <div class="chart-card">
            <h3 class="chart-title">Tickets par Jour</h3>
            <div class="chart-container">
                <canvas id="ticketsPerDayChart"></canvas>
            </div>
        </div>
        
        <!-- Tickets by platform chart -->
        <div class="chart-card">
            <h3 class="chart-title">Tickets par Plateforme</h3>
            <div class="chart-container">
                <canvas id="ticketsByPlatformChart"></canvas>
            </div>
        </div>
        
        <!-- Tickets by priority chart -->
        <div class="chart-card">
            <h3 class="chart-title">Tickets par Priorité</h3>
            <div class="chart-container">
                <canvas id="ticketsByPriorityChart"></canvas>
            </div>
        </div>
        
        <!-- Technicians performance chart -->
        <div class="chart-card">
            <h3 class="chart-title">Performance des Techniciens</h3>
            <div class="chart-container">
                <canvas id="techniciansPerformanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.filter-section {
    margin-bottom: 2rem;
}

.filter-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
}

.filter-form {
    margin: 0;
}

.filter-row {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.stats-overview {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-icon.blue { background: #3b82f6; }
.stat-icon.green { background: #10b981; }
.stat-icon.orange { background: #f59e0b; }
.stat-icon.red { background: #ef4444; }
.stat-icon.purple { background: #8b5cf6; }
.stat-icon.teal { background: #14b8a6; }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.export-section {
    margin-bottom: 2rem;
}

.export-header {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.section-description {
    color: #6b7280;
    font-size: 0.875rem;
}

.export-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
}

.export-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.export-icon {
    width: 64px;
    height: 64px;
    background: #f3f4f6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    margin-bottom: 1rem;
}

.export-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.export-description {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.export-actions {
    margin-top: 1rem;
}

.export-buttons {
    display: flex;
    gap: 0.5rem;
}

.charts-section {
    margin-bottom: 2rem;
}

.charts-header {
    margin-bottom: 1.5rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.chart-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
}

.chart-container {
    height: 300px;
    position: relative;
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

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .export-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .export-buttons {
        flex-direction: column;
    }
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCharts();
});

function loadCharts() {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    
    // Load tickets per day chart
    fetch(`/reports/api-data?start_date=${startDate}&end_date=${endDate}&type=tickets_par_jour`)
        .then(response => response.json())
        .then(data => {
            createTicketsPerDayChart(data);
        })
        .catch(error => console.error('Error loading tickets per day:', error));
    
    // Load tickets by platform chart
    fetch(`/reports/api-data?start_date=${startDate}&end_date=${endDate}&type=tickets_par_plateforme`)
        .then(response => response.json())
        .then(data => {
            createTicketsByPlatformChart(data);
        })
        .catch(error => console.error('Error loading tickets by platform:', error));
    
    // Load tickets by priority chart
    fetch(`/reports/api-data?start_date=${startDate}&end_date=${endDate}&type=tickets_par_priorite`)
        .then(response => response.json())
        .then(data => {
            createTicketsByPriorityChart(data);
        })
        .catch(error => console.error('Error loading tickets by priority:', error));
    
    // Load technicians performance chart
    fetch(`/reports/api-data?start_date=${startDate}&end_date=${endDate}&type=performance_techniciens`)
        .then(response => response.json())
        .then(data => {
            createTechniciansPerformanceChart(data);
        })
        .catch(error => console.error('Error loading technicians performance:', error));
}

function createTicketsPerDayChart(data) {
    const ctx = document.getElementById('ticketsPerDayChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.date),
            datasets: [{
                label: 'Tickets par jour',
                data: data.map(item => item.count),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function createTicketsByPlatformChart(data) {
    const ctx = document.getElementById('ticketsByPlatformChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                data: data.map(item => item.value),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function createTicketsByPriorityChart(data) {
    const ctx = document.getElementById('ticketsByPriorityChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                label: 'Tickets par priorité',
                data: data.map(item => item.value),
                backgroundColor: [
                    '#ef4444',
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function createTechniciansPerformanceChart(data) {
    const ctx = document.getElementById('techniciansPerformanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Total', 'Résolus', 'Taux (%)'],
            datasets: data.map((technician, index) => ({
                label: technician.name,
                data: [technician.total, technician.resolved, technician.rate],
                borderColor: `hsl(${index * 60}, 70%, 50%)`,
                backgroundColor: `hsla(${index * 60}, 70%, 50%, 0.2)`
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>
@endsection
