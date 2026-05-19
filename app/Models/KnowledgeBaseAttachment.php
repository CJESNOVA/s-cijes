<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseAttachment extends Model
{
    protected $fillable = [
        'article_id',
        'nom_fichier',
        'nom_original',
        'chemin',
        'mime_type',
        'taille',
        'description',
        'is_primary',
    ];

    protected $casts = [
        'taille' => 'integer',
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class, 'article_id');
    }

    /**
     * Scopes
     */
    public function scopeByArticle($query, $articleId)
    {
        return $query->where('article_id', $articleId);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
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

    public function getTailleFormateeAttribute(): string
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

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->isImage()) {
            // Pour les images, retourner l'URL directe
            return $this->url;
        }
        
        // Pour les documents, retourner une icône générique
        return match($this->mime_type) {
            'application/pdf' => '/images/icons/pdf-icon.svg',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '/images/icons/doc-icon.svg',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel' => '/images/icons/excel-icon.svg',
            default => '/images/icons/file-icon.svg',
        };
    }

    /**
     * Methods
     */
    public function formatSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
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

    public function makePrimary(): void
    {
        // Désactiver tous les autres fichiers primaires de cet article
        static::where('article_id', $this->article_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        // Activer ce fichier comme primaire
        $this->update(['is_primary' => true]);
    }

    /**
     * Static methods
     */
    public static function getStoragePath(): string
    {
        return 'attachments/knowledge-base/';
    }

    public static function getMaxFileSize(): int
    {
        return 20 * 1024 * 1024; // 20MB pour la KB
    }

    public static function getAllowedMimeTypes(): array
    {
        return [
            // Images (plus pour la KB)
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
            'image/bmp',
            'image/tiff',
            
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'text/rtf',
            
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
            'application/xml',
        ];
    }

    public static function getImageMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
            'image/bmp',
            'image/tiff',
        ];
    }
}
