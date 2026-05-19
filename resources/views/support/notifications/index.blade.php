@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Gérez toutes vos notifications en un seul endroit</p>
        </div>
        <div class="header-right">
            <button class="btn btn-secondary" data-action="mark-all-read" data-testid="mark-all-read-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 14 4 12"/>
                </svg>
                Marquer tout comme lu
            </button>
            <button class="btn btn-danger" data-action="clear-all" data-testid="clear-all-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Vider tout
            </button>
        </div>
    </div>
</header>

<!-- Stats Cards -->
<div class="stats-grid" data-testid="stats-grid">
    <div class="stat-card" data-testid="stat-total">
        <div class="stat-header">
            <span class="stat-label">Total Notifications</span>
            <div class="stat-icon total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-change">Toutes vos notifications</div>
    </div>

    <div class="stat-card" data-testid="stat-unread">
        <div class="stat-header">
            <span class="stat-label">Non lues</span>
            <div class="stat-icon unread">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['unread'] }}</div>
        <div class="stat-change">Notifications à lire</div>
    </div>

    <div class="stat-card" data-testid="stat-read">
        <div class="stat-header">
            <span class="stat-label">Lues</span>
            <div class="stat-icon read">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 14 4 12"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['read'] }}</div>
        <div class="stat-change">Notifications déjà lues</div>
    </div>
</div>

<!-- Filters Section -->
<div class="filters-container" data-testid="filters-container">
    <form method="GET" action="{{ route('notifications.page') }}" class="w-full">
        <div class="filters-row">
            <select name="type" class="filter-select" data-testid="filter-type">
                <option value="">Tous les types</option>
                @foreach($types as $type)
                    <option value="{{ $type->type }}" {{ request('type') == $type->type ? 'selected' : '' }}>
                        {{ \App\Helpers\NotificationHelper::getTypeLabel($type->type) }} ({{ $type->count }})
                    </option>
                @endforeach
            </select>
            
            <select name="read" class="filter-select" data-testid="filter-read">
                <option value="">Tous les statuts</option>
                <option value="0" {{ request('read') == '0' ? 'selected' : '' }}>Non lues</option>
                <option value="1" {{ request('read') == '1' ? 'selected' : '' }}>Lues</option>
            </select>
            
            <button type="button" onclick="window.location.href='{{ route('notifications.page') }}'" class="btn btn-secondary" data-testid="clear-filters">
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

<!-- Notifications List -->
<div class="notifications-list-container" data-testid="notifications-list-container">
    @forelse($notifications as $notification)
        <div class="notification-card {{ $notification->read ? 'read' : 'unread' }}" data-testid="notification-{{ $notification->id }}">
            <div class="notification-left">
                <div class="notification-icon notification-{{ $notification->color }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        {!! \App\Helpers\NotificationHelper::getIconSvg($notification->icon) !!}
                    </svg>
                </div>
                @if(!$notification->read)
                    <div class="notification-indicator"></div>
                @endif
            </div>
            
            <div class="notification-content">
                <div class="notification-header">
                    <h3 class="notification-title">{{ $notification->title }}</h3>
                    <div class="notification-meta">
                        <span class="notification-type">{{ \App\Helpers\NotificationHelper::getTypeLabel($notification->type) }}</span>
                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                
                <p class="notification-message">{{ $notification->message }}</p>
                
                @if($notification->notifiable)
                    <div class="notification-link">
                        @if($notification->notifiable_type === 'App\Models\Ticket')
                            <a href="{{ route('tickets.show', $notification->notifiable->id) }}" class="btn btn-sm btn-outline">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8-11 8-11 8-11 8-11 8-11 8-11 8-11 11 8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Voir le ticket {{ $notification->notifiable->reference }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            
            <div class="notification-actions">
                @if(!$notification->read)
                    <button class="btn btn-sm btn-primary" data-action="mark-read" data-notification-id="{{ $notification->id }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 14 4 12"/>
                        </svg>
                        Marquer comme lu
                    </button>
                @endif
                <button class="btn btn-sm btn-danger" data-action="delete" data-notification-id="{{ $notification->id }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto text-gray-400 mb-4">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune notification</h3>
            <p class="text-gray-500 mb-4">Vous n'avez aucune notification pour le moment.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                Retour au dashboard
            </a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($notifications->hasPages())
    <div class="pagination" data-testid="pagination">
        <div class="pagination-info">
            Affichage de <strong>{{ $notifications->firstItem() }}</strong> à <strong>{{ $notifications->lastItem() }}</strong> sur <strong>{{ $notifications->total() }}</strong> notifications
        </div>
        <div class="pagination-controls">
            {{ $notifications->links() }}
        </div>
    </div>
@endif

<!-- Styles supplémentaires pour la page -->
<style>
/* Utiliser les classes existantes pour la cohérence */
.filters-container {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
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

.stat-card:nth-child(2) {
    border-left-color: #ef4444;
}

.stat-card:nth-child(3) {
    border-left-color: #10b981;
}

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
.stat-icon.unread { background: #ef4444; }
.stat-icon.read { background: #10b981; }

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

.notifications-list-container {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.notification-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s;
    position: relative;
}

.notification-card:hover {
    background: #f9fafb;
}

.notification-card.unread {
    background: #f0f9ff;
}

.notification-card.unread::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #3b82f6;
}

.notification-card:last-child {
    border-bottom: none;
}

.notification-left {
    position: relative;
    flex-shrink: 0;
}

.notification-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.notification-indicator {
    position: absolute;
    top: 0;
    right: 0;
    width: 12px;
    height: 12px;
    background: #3b82f6;
    border: 2px solid white;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.notification-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.notification-type {
    font-size: 0.75rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
}

.notification-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

.notification-message {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.notification-link {
    margin-bottom: 1rem;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

@media (max-width: 768px) {
    .notification-card {
        flex-direction: column;
        gap: 1rem;
    }
    
    .notification-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .notification-meta {
        align-items: flex-start;
    }
    
    .notification-actions {
        align-self: stretch;
        justify-content: flex-end;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marquer tout comme lu
    document.querySelector('[data-action="mark-all-read"]')?.addEventListener('click', async function() {
        try {
            const response = await fetch('/api/notifications/read-all', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });
    
    // Vider tout
    document.querySelector('[data-action="clear-all"]')?.addEventListener('click', async function() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer toutes vos notifications ?')) {
            return;
        }
        
        try {
            const response = await fetch('/api/notifications/clear', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error clearing notifications:', error);
        }
    });
    
    // Marquer comme lu
    document.querySelectorAll('[data-action="mark-read"]').forEach(button => {
        button.addEventListener('click', async function() {
            const notificationId = this.dataset.notificationId;
            
            try {
                const response = await fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });
    
    // Supprimer
    document.querySelectorAll('[data-action="delete"]').forEach(button => {
        button.addEventListener('click', async function() {
            const notificationId = this.dataset.notificationId;
            
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        });
    });
});
</script>
@endsection
