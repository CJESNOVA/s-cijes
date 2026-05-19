<?php

namespace App\Helpers;

class NotificationHelper
{
    /**
     * Obtenir le label d'un type de notification
     */
    public static function getTypeLabel($type)
    {
        $labels = [
            'ticket_created' => 'Ticket créé',
            'ticket_assigned' => 'Ticket assigné',
            'message_added' => 'Nouveau message',
            'status_changed' => 'Statut modifié',
            'ticket_resolved' => 'Ticket résolu',
            'ticket_closed' => 'Ticket fermé',
            'urgent_ticket' => 'Ticket urgent',
            'system_maintenance' => 'Maintenance',
        ];
        return $labels[$type] ?? $type;
    }

    /**
     * Obtenir le SVG d'une icône de notification
     */
    public static function getIconSvg($iconName)
    {
        $icons = [
            'plus-circle' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
            'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><line x1="8.5" y1="7" x2="16" y2="7"/><polyline points="11 4 14 7 11 10"/>',
            'message-circle' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
            'activity' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
            'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'x-circle' => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01 17"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m9-9h-6m-6 0H3m16.24-6.76l-4.24 4.24M9 9 4.76 4.76m14.48 14.48L15 15M9 15l-4.24 4.24"/>',
            'bell' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'
        ];
        return $icons[$iconName] ?? $icons['bell'];
    }
}
