<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketFichier extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'nom',
        'chemin',
        'extension',
        'taille',
    ];

    protected $casts = [
        'taille' => 'integer',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->chemin);
    }

    public function getTailleFormateeAttribute()
    {
        $bytes = $this->taille;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file is an image
     */
    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp', 'tiff'];
        return in_array(strtolower($this->extension), $imageExtensions);
    }

    /**
     * Check if the file is a document
     */
    public function isDocument()
    {
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv'];
        return in_array(strtolower($this->extension), $documentExtensions);
    }

    /**
     * Check if the file is an archive
     */
    public function isArchive()
    {
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        return in_array(strtolower($this->extension), $archiveExtensions);
    }

    /**
     * Get the MIME type based on extension
     */
    public function getMimeType()
    {
        $extension = strtolower($this->extension);
        
        return match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            default => 'application/octet-stream',
        };
    }

    /**
     * Get the icon class based on file type
     */
    public function getIconClass()
    {
        if ($this->isImage()) {
            return 'text-blue-600';
        } elseif ($this->isDocument()) {
            return 'text-green-600';
        } elseif ($this->isArchive()) {
            return 'text-orange-600';
        } else {
            return 'text-gray-600';
        }
    }

    /**
     * Get the thumbnail URL for images
     */
    public function getThumbnailUrl()
    {
        if ($this->isImage()) {
            return $this->url;
        }
        
        return match($this->extension) {
            'pdf' => '/images/icons/pdf-icon.svg',
            'doc', 'docx' => '/images/icons/doc-icon.svg',
            'xls', 'xlsx' => '/images/icons/excel-icon.svg',
            'ppt', 'pptx' => '/images/icons/ppt-icon.svg',
            'zip', 'rar', '7z' => '/images/icons/zip-icon.svg',
            default => '/images/icons/file-icon.svg',
        };
    }

    /**
     * Get the download name
     */
    public function getDownloadName()
    {
        return $this->nom;
    }
}
