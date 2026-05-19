<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistorique extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'action', 'details'];

    // Cast automatique du JSON pour manipuler les détails comme un tableau PHP
    protected $casts = ['details' => 'array'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function user() { return $this->belongsTo(User::class); }

    /**
     * Obtenir le type d'événement pour le filtrage
     */
    public function getEventType()
    {
        $action = strtolower($this->action);
        
        if (strpos($action, 'statut') !== false) return 'statut';
        if (strpos($action, 'priorité') !== false || strpos($action, 'priority') !== false) return 'priority';
        if (strpos($action, 'assign') !== false) return 'assignment';
        if (strpos($action, 'message') !== false) return 'message';
        if (strpos($action, 'fichier') !== false || strpos($action, 'file') !== false) return 'file';
        if (strpos($action, 'satisfaction') !== false) return 'satisfaction';
        
        return 'default';
    }

    /**
     * Obtenir la couleur de l'événement
     */
    public function getEventColor()
    {
        return $this->getEventType();
    }

    /**
     * Obtenir l'icône de l'événement
     */
    public function getEventIcon()
    {
        $type = $this->getEventType();
        
        switch($type) {
            case 'statut':
                return '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>';
            case 'priority':
                return '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>';
            case 'assignment':
                return '<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>';
            case 'message':
                return '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>';
            case 'file':
                return '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>';
            case 'satisfaction':
                return '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>';
            default:
                return '<circle cx="12" cy="12" r="10"/>';
        }
    }

    /**
     * Formater l'action pour l'affichage
     */
    public function getFormattedAction()
    {
        return ucfirst($this->action);
    }

    /**
     * Formater les détails pour l'affichage
     */
    public function getFormattedDetails()
    {
        if (!$this->details) return '';
        
        if (is_string($this->details)) {
            return $this->details;
        }
        
        if (is_array($this->details)) {
            $formatted = [];
            
            foreach ($this->details as $key => $value) {
                switch($key) {
                    case 'old_status':
                    case 'new_status':
                        $formatted[] = "Statut: {$value}";
                        break;
                    case 'old_priority':
                    case 'new_priority':
                        $formatted[] = "Priorité: {$value}";
                        break;
                    case 'assigned_to':
                        $formatted[] = "Assigné à: {$value}";
                        break;
                    case 'file_name':
                        $formatted[] = "Fichier: {$value}";
                        break;
                    case 'satisfaction_note':
                        $formatted[] = "Note: {$value}/5";
                        break;
                    default:
                        $formatted[] = "{$key}: {$value}";
                }
            }
            
            return implode(' | ', $formatted);
        }
        
        return json_encode($this->details);
    }
}
