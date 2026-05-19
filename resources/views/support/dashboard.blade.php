@extends('layouts.app')

@section('content')
<!-- Message de succès -->
@if(session('success'))
<div class="notification-badge" style="position: fixed; top: 20px; right: 20px; background: #059669; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    {{ session('success') }}
</div>
@endif

<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Vue d'ensemble de l'activité</p>
        </div>
        <div class="header-right">
            <button class="icon-button" data-testid="notifications-button" id="notificationsButton">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notification-badge" id="notificationCount">0</span>
            </button>
            
            <!-- Dropdown Notifications -->
            <div class="notifications-dropdown" id="notificationsDropdown" style="display: none;">
                <div class="notifications-header">
                    <h4>Notifications</h4>
                    <a href="{{ route('notifications.page') }}" class="view-all-link">Voir tout</a>
                </div>
                <div class="notifications-list" id="notificationsList">
                    <div class="notification-item loading">
                        <div class="loading-spinner"></div>
                        <span>Chargement...</span>
                    </div>
                </div>
                <div class="notifications-footer">
                    <button class="btn btn-sm btn-secondary" id="markAllReadBtn">Marquer tout comme lu</button>
                </div>
            </div>
            
            <!-- Bouton de déconnexion dans le header -->
            <form action="{{ route('logout') }}" method="POST" class="inline" data-testid="header-logout-form">
                @csrf
                <button type="submit" class="icon-button logout-header-btn" data-testid="header-logout-btn" title="Déconnexion">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16,17 21,12 16,7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Filters Section -->
<div class="filters-container" data-testid="filters-container">
    <form method="GET" action="{{ route('dashboard') }}" class="w-full">
        <div class="search-bar">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" name="search" class="search-input" placeholder="Rechercher un ticket..." value="{{ request('search') }}" data-testid="search-input">
        </div>
        
        <div class="filters-row">
            <select name="plateforme" class="filter-select" data-testid="filter-platform">
                <option value="">Toutes les plateformes</option>
                @foreach($filterData['plateformes'] as $p)
                    <option value="{{ $p->id }}" {{ request('plateforme') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                @endforeach
            </select>
            
            <select name="statut" class="filter-select" data-testid="filter-status">
                <option value="">Tous les statuts</option>
                @foreach($filterData['statuts'] as $s)
                    <option value="{{ $s->id }}" {{ request('statut') == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                @endforeach
            </select>
            
            <select name="priorite" class="filter-select" data-testid="filter-priority">
                <option value="">Toutes les priorités</option>
                @foreach($filterData['priorites'] as $prio)
                    <option value="{{ $prio->id }}" {{ request('priorite') == $prio->id ? 'selected' : '' }}>{{ $prio->nom }}</option>
                @endforeach
            </select>
            
            <button type="button" onclick="window.location.href='{{ route('dashboard') }}'" class="btn btn-secondary" data-testid="clear-filters">
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

<!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid" data-testid="stats-grid">
        <div class="stat-card" data-testid="stat-total-tickets">
            <div class="stat-header">
                <span class="stat-label">Total Tickets</span>
                <svg class="stat-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-value">{{ $tickets->total() ?? 0 }}</div>
            <div class="stat-change positive">+12% ce mois</div>
        </div>

        <div class="stat-card" data-testid="stat-open-tickets">
            <div class="stat-header">
                <span class="stat-label">Tickets Ouverts</span>
                <svg class="stat-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 16 14"/>
                </svg>
            </div>
            <div class="stat-value">{{ $tickets->where('statut_id', function($query) {
                    return $query->where('code', 'ouvert');
                })->count() ?? 0 }}</div>
            <div class="stat-change neutral">-3% vs hier</div>
        </div>

        <div class="stat-card" data-testid="stat-urgent-tickets">
            <div class="stat-header">
                <span class="stat-label">Tickets Urgents</span>
                <svg class="stat-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="stat-value">{{ $tickets->where('priorite_id', function($query) {
                    return $query->where('niveau', 3);
                })->count() ?? 0 }}</div>
            <div class="stat-change negative">+5 aujourd'hui</div>
        </div>

        <div class="stat-card" data-testid="stat-resolved-tickets">
            <div class="stat-header">
                <span class="stat-label">Tickets Résolus</span>
                <svg class="stat-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="stat-value">{{ $tickets->where('statut_id', function($query) {
                    return $query->where('code', 'resolu');
                })->count() ?? 0 }}</div>
            <div class="stat-change positive">+18% ce mois</div>
        </div>
    </div>

    <!-- Charts and Activity -->
    <div class="dashboard-grid">
        <!-- Chart: Tickets par Plateforme -->
        <div class="chart-card" data-testid="chart-platforms">
            <div class="card-header">
                <h3 class="card-title">Tickets par Plateforme</h3>
                <select class="select-small" data-testid="chart-period-filter" id="platformPeriodFilter">
                    <option value="7">7 derniers jours</option>
                    <option value="30">30 derniers jours</option>
                    <option value="month">Ce mois</option>
                </select>
            </div>
            <div class="chart-content">
                <div class="chart-bar-group">
                    @php
                        $platformStats = $tickets->groupBy('plateforme_id')->map(function($group) {
                            return [
                                'name' => $group->first()->plateforme->nom,
                                'count' => $group->count()
                            ];
                        })->take(4);
                    @endphp
                    @foreach($platformStats as $stat)
                    <div class="chart-bar-item">
                        <div class="chart-bar-label">{{ $stat['name'] }}</div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="width: {{ $stat['count'] / $tickets->count() * 100 }}%; background: #2563EB;"></div>
                            <span class="chart-bar-value">{{ $stat['count'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chart: Tickets par Statut -->
        <div class="chart-card" data-testid="chart-status">
            <div class="card-header">
                <h3 class="card-title">Tickets par Statut</h3>
            </div>
            <div class="chart-content">
                <div class="donut-chart">
                    @php
                        $statusStats = $tickets->groupBy('statut_id')->map(function($group) {
                            return [
                                'name' => $group->first()->statut->nom,
                                'count' => $group->count()
                            ];
                        });
                    @endphp
                    <div class="donut-legend">
                        @foreach($statusStats as $stat)
                        <div class="legend-item">
                            <span class="legend-color" style="background: {{ $stat['count'] / $tickets->count() > 0.3 ? '#DC2626' : ($stat['count'] / $tickets->count() > 0.2 ? '#D97706' : '#2563EB') }};"></span>
                            <span class="legend-label">{{ $stat['name'] }}</span>
                            <span class="legend-value">{{ $stat['count'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-card" data-testid="recent-activity">
            <div class="card-header">
                <h3 class="card-title">Activité Récente</h3>
                <a href="{{ route('my-tickets.index') }}" class="link-small" data-testid="view-all-activity">Voir tout</a>
            </div>
            <div class="activity-list">
                @forelse($tickets->take(5) as $ticket)
                <div class="activity-item" data-testid="activity-item">
                    <div class="activity-icon" style="background: #ECFDF5; color: #059669;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            Ticket <strong><a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline">#{{ $ticket->reference }}</a></strong> {{ $ticket->statut->code === 'resolu' ? 'résolu' : 'créé' }} par {{ $ticket->demandeur->nom }} {{ $ticket->demandeur->prenom }}
                        </div>
                        <div class="activity-time">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <div class="activity-content">
                        <div class="activity-text">Aucune activité récente</div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- Styles pour les filtres du dashboard -->
<style>
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

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-secondary {
    background: white;
    color: #374151;
    border-color: #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

/* Notifications Dropdown Styles */
.notifications-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 320px;
    max-height: 400px;
    z-index: 1000;
    margin-top: 8px;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
}

.notifications-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.view-all-link {
    font-size: 12px;
    color: #3b82f6;
    text-decoration: none;
}

.view-all-link:hover {
    text-decoration: underline;
}

.notifications-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f9fafb;
}

.notification-item.unread {
    background-color: #eff6ff;
    border-left: 3px solid #3b82f6;
}

.notification-item.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: #6b7280;
}

.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.notification-content {
    font-size: 13px;
    line-height: 1.4;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 2px;
}

.notification-message {
    color: #6b7280;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 11px;
    color: #9ca3af;
}

.notifications-footer {
    padding: 12px 16px;
    border-top: 1px solid #e5e7eb;
    text-align: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationsButton = document.getElementById('notificationsButton');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    const notificationCount = document.getElementById('notificationCount');
    const notificationsList = document.getElementById('notificationsList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    // Toggle dropdown
    notificationsButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = notificationsDropdown.style.display === 'block';
        notificationsDropdown.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            loadNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationsDropdown.contains(e.target) && e.target !== notificationsButton) {
            notificationsDropdown.style.display = 'none';
        }
    });

    // Load notifications
    function loadNotifications() {
        notificationsList.innerHTML = `
            <div class="notification-item loading">
                <div class="loading-spinner"></div>
                <span>Chargement...</span>
            </div>
        `;

        fetch('/api/notifications')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.unread_count || 0);
                displayNotifications(data.notifications || []);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsList.innerHTML = `
                    <div class="notification-item">
                        <div class="notification-content">
                            <div class="notification-message">Erreur lors du chargement des notifications</div>
                        </div>
                    </div>
                `;
            });
    }

    // Update notification count
    function updateNotificationCount(count) {
        notificationCount.textContent = count;
        notificationCount.style.display = count > 0 ? 'block' : 'none';
    }

    // Display notifications
    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            notificationsList.innerHTML = `
                <div class="notification-item">
                    <div class="notification-content">
                        <div class="notification-message">Aucune notification</div>
                    </div>
                </div>
            `;
            return;
        }

        notificationsList.innerHTML = notifications.map(notification => `
            <div class="notification-item ${!notification.read ? 'unread' : ''}" data-notification-id="${notification.id}">
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${formatTime(notification.created_at)}</div>
                </div>
            </div>
        `).join('');

        // Add click handlers for marking as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.notificationId;
                if (notificationId && this.classList.contains('unread')) {
                    markAsRead(notificationId);
                }
            });
        });
    }

    // Format time
    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Il y a quelques instants';
        if (diff < 3600000) return `Il y a ${Math.floor(diff / 60000)} min`;
        if (diff < 86400000) return `Il y a ${Math.floor(diff / 3600000)} h`;
        return `Il y a ${Math.floor(diff / 86400000)} j`;
    }

    // Mark notification as read
    function markAsRead(notificationId) {
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                }
                updateNotificationCount(Math.max(0, parseInt(notificationCount.textContent) - 1));
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        fetch('/api/notifications/read-all', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                updateNotificationCount(0);
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    });

    // Auto-refresh notifications every 30 seconds
    setInterval(function() {
        fetch('/api/notifications/count')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.count || 0);
            })
            .catch(error => {
                console.error('Error refreshing notification count:', error);
            });
    }, 30000);

    // Filtrage par période pour les graphiques
    const periodFilter = document.getElementById('platformPeriodFilter');
    if (periodFilter) {
        periodFilter.addEventListener('change', function() {
            const period = this.value;
            updatePlatformChart(period);
        });
    }

    function updatePlatformChart(period) {
        // Afficher un indicateur de chargement
        const chartContent = document.querySelector('[data-testid="chart-platforms"] .chart-content');
        const originalContent = chartContent.innerHTML;
        
        chartContent.innerHTML = '<div class="text-center py-8"><div class="loading-spinner"></div><p class="text-sm text-gray-500 mt-2">Chargement...</p></div>';
        
        // Calculer la date selon la période
        let startDate;
        const today = new Date();
        
        if (period === '7') {
            startDate = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        } else if (period === '30') {
            startDate = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
        } else if (period === 'month') {
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        }
        
        // Simuler un appel API pour obtenir les données filtrées
        setTimeout(() => {
            // En production, ceci serait un appel fetch vers une API
            fetch(`/api/dashboard/platform-stats?period=${period}&start=${startDate.toISOString()}`)
                .then(response => response.json())
                .then(data => {
                    renderPlatformChart(data);
                })
                .catch(error => {
                    console.error('Error loading platform stats:', error);
                    // En cas d'erreur, restaurer le contenu original
                    chartContent.innerHTML = originalContent;
                });
        }, 500);
    }

    function renderPlatformChart(data) {
        const chartContent = document.querySelector('[data-testid="chart-platforms"] .chart-content');
        
        let html = '<div class="chart-bar-group">';
        data.forEach(stat => {
            const percentage = stat.total > 0 ? (stat.count / stat.total * 100) : 0;
            html += `
                <div class="chart-bar-item">
                    <div class="chart-bar-label">${stat.name}</div>
                    <div class="chart-bar-wrapper">
                        <div class="chart-bar" style="width: ${percentage}%; background: #2563EB;"></div>
                        <span class="chart-bar-value">${stat.count}</span>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        chartContent.innerHTML = html;
    }

    // Initial load
    loadNotifications();
});
</script>
@endsection
