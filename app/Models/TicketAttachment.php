<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'nom_fichier',
        'nom_original',
        'chemin',
        'mime_type',
        'taille',
    ];

    protected $casts = [
        'taille' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Scopes
     */
    public function scopeByTicket($query, $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    public function scopeOrderByDate($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Accessors & Mutators
     */
    public function getUrlAttribute(): string
    {
        return '/storage/' . $this->chemin;
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->formatSize($this->taille);
    }

    public function getIconClassAttribute(): string
    {
        $extension = pathinfo($this->nom_fichier, PATHINFO_EXTENSION);
        return match(strtolower($extension)) {
            'pdf' => 'text-red-600',
            'doc', 'docx' => 'text-blue-600',
            'xls', 'xlsx' => 'text-green-600',
            'png', 'jpg', 'jpeg', 'gif', 'svg' => 'text-gray-600',
            'zip', 'rar', '7z', 'tar', 'gz' => 'text-orange-600',
            default => 'text-gray-400',
        };
    }

    /**
     * Methods
     */
    public function formatSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes < 1024 && $bytes > 0; $bytes /= 1024) {
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
        ]);
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ]);
    }

    public function isArchive(): bool
    {
        return in_array($this->mime_type, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip',
        ]);
    }

    public function getDownloadName(): string
    {
        return $this->nom_original ?: $this->nom_fichier;
    }

    /**
     * Static methods
     */
    public static function getStoragePath(): string
    {
        return 'attachments/tickets/';
    }

    public static function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }

    public static function getAllowedMimeTypes(): array
    {
        return [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
            
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            
            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip',
            
            // Others
            'text/html',
            'text/css',
            'application/javascript',
            'application/json',
        ];
    }
}
