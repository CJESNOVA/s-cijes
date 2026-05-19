class NotificationSystem {
    constructor() {
        this.unreadCount = 0;
        this.notifications = [];
        this.dropdownOpen = false;
        this.init();
    }

    init() {
        this.loadNotificationCount();
        this.setupEventListeners();
        this.setupRealTimeUpdates();
    }

    setupEventListeners() {
        // Bouton notifications
        const notificationBtn = document.querySelector('[data-testid="notifications-button"]');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown();
            });
        }

        // Click outside pour fermer le dropdown
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.notifications-dropdown') && !e.target.closest('[data-testid="notifications-button"]')) {
                this.closeDropdown();
            }
        });

        // Marquer comme lu au clic sur une notification
        document.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem && !notificationItem.classList.contains('read')) {
                this.markAsRead(notificationItem.dataset.notificationId);
            }
        });

        // Bouton "Marquer tout comme lu"
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="mark-all-read"]')) {
                this.markAllAsRead();
            }
        });

        // Bouton "Vider tout"
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="clear-all"]')) {
                this.clearAll();
            }
        });
    }

    setupRealTimeUpdates() {
        // Polling toutes les 30 secondes pour les nouvelles notifications
        setInterval(() => {
            this.loadNotificationCount();
        }, 30000);

        // Écouter les événements de notification (WebSocket future)
        this.setupWebSocket();
    }

    setupWebSocket() {
        // Placeholder pour WebSocket implementation future
        // Pour l'instant, on utilise le polling
        console.log('WebSocket setup - using polling for now');
    }

    async loadNotificationCount() {
        try {
            const response = await fetch('/api/notifications/count');
            const data = await response.json();
            
            this.updateBadge(data.unread_count);
            this.unreadCount = data.unread_count;
        } catch (error) {
            console.error('Error loading notification count:', error);
        }
    }

    async loadRecentNotifications() {
        try {
            const response = await fetch('/api/notifications/recent');
            const data = await response.json();
            
            this.notifications = data.notifications;
            this.renderDropdown();
            this.updateBadge(data.unread_count);
        } catch (error) {
            console.error('Error loading recent notifications:', error);
        }
    }

    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
                badge.classList.add('has-notifications');
            } else {
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    }

    toggleDropdown() {
        if (this.dropdownOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    openDropdown() {
        this.closeDropdown(); // Fermer d'abord les autres dropdowns
        
        const dropdown = this.createDropdown();
        document.body.appendChild(dropdown);
        
        // Positionner le dropdown
        const button = document.querySelector('[data-testid="notifications-button"]');
        const rect = button.getBoundingClientRect();
        
        dropdown.style.top = `${rect.bottom + 8}px`;
        dropdown.style.right = `${window.innerWidth - rect.right}px`;
        dropdown.style.display = 'block';
        
        this.dropdownOpen = true;
        this.loadRecentNotifications();
    }

    closeDropdown() {
        const existingDropdown = document.querySelector('.notifications-dropdown');
        if (existingDropdown) {
            existingDropdown.remove();
        }
        this.dropdownOpen = false;
    }

    createDropdown() {
        const dropdown = document.createElement('div');
        dropdown.className = 'notifications-dropdown';
        dropdown.innerHTML = `
            <div class="notifications-header">
                <h3>Notifications</h3>
                <div class="notifications-actions">
                    <button data-action="mark-all-read" class="btn-link" title="Marquer tout comme lu">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </button>
                    <button data-action="clear-all" class="btn-link" title="Vider tout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="notifications-list">
                <div class="loading">Chargement...</div>
            </div>
            <div class="notifications-footer">
                <a href="/notifications" class="view-all-link">Voir toutes les notifications</a>
            </div>
        `;
        
        return dropdown;
    }

    renderDropdown() {
        const listContainer = document.querySelector('.notifications-list');
        if (!listContainer) return;

        if (this.notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="empty-notifications">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>Aucune notification</p>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = this.notifications.map(notification => `
            <div class="notification-item ${notification.read ? 'read' : 'unread'}" data-notification-id="${notification.id}">
                <div class="notification-icon notification-${notification.color}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        ${this.getIconSvg(notification.icon)}
                    </svg>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${this.formatTime(notification.created_at)}</div>
                </div>
                ${!notification.read ? '<div class="notification-indicator"></div>' : ''}
            </div>
        `).join('');
    }

    getIconSvg(iconName) {
        const icons = {
            'plus-circle': '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
            'user-plus': '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><line x1="8.5" y1="7" x2="16" y2="7"/><polyline points="11 4 14 7 11 10"/>',
            'message-circle': '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
            'activity': '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
            'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'x-circle': '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            'alert-triangle': '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01 17"/>',
            'settings': '<circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m9-9h-6m-6 0H3m16.24-6.76l-4.24 4.24M9 9 4.76 4.76m14.48 14.48L15 15M9 15l-4.24 4.24"/>',
            'bell': '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'
        };
        return icons[iconName] || icons['bell'];
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'À l\'instant';
        if (minutes < 60) return `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
        if (hours < 24) return `Il y a ${hours} heure${hours > 1 ? 's' : ''}`;
        if (days < 7) return `Il y a ${days} jour${days > 1 ? 's' : ''}`;
        
        return date.toLocaleDateString('fr-FR');
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateBadge(data.unread_count);
                
                // Mettre à jour l'UI
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.add('read');
                    notificationItem.classList.remove('unread');
                    const indicator = notificationItem.querySelector('.notification-indicator');
                    if (indicator) indicator.remove();
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/api/notifications/read-all', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateBadge(data.unread_count);
                this.loadRecentNotifications(); // Recharger pour mettre à jour l'UI
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    async clearAll() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer toutes vos notifications ?')) {
            return;
        }
        
        try {
            const response = await fetch('/api/notifications/clear', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateBadge(0);
                this.notifications = [];
                this.renderDropdown();
            }
        } catch (error) {
            console.error('Error clearing notifications:', error);
        }
    }

    // Créer une notification (pour les tests)
    static async createNotification(userId, type, title, message, data = {}) {
        try {
            const response = await fetch('/api/notifications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    user_id: userId,
                    type,
                    title,
                    message,
                    data
                })
            });
            
            return await response.json();
        } catch (error) {
            console.error('Error creating notification:', error);
        }
    }
}

// Initialiser le système de notifications quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
});

// Exporter pour utilisation globale
window.NotificationSystem = NotificationSystem;
