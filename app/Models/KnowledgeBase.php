<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KnowledgeBase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'author_id',
        'updater_id',
        'tags',
        'attachment_path',
        'published',
        'published_at',
        'views_count',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'published_at',
    ];

    /**
     * Get the category that owns the article.
     */
    public function category()
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'category_id');
    }

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the user who last updated the article.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updater_id');
    }

    /**
     * Get the attachments for this article.
     */
    public function attachments()
    {
        return $this->hasMany(KnowledgeBaseAttachment::class, 'article_id');
    }

    /**
     * Get the primary attachment for this article.
     */
    public function primaryAttachment()
    {
        return $this->hasOne(KnowledgeBaseAttachment::class, 'article_id')->primary();
    }

    /**
     * Get the thumbnail URL for the attachment.
     */
    public function getAttachmentThumbnailUrlAttribute(): ?string
    {
        if ($this->attachment_path && $this->isImage()) {
            return $this->attachment_url;
        }
        return null;
    }

    /**
     * Get the attachment name from the path.
     */
    public function getAttachmentNameAttribute(): ?string
    {
        if ($this->attachment_path) {
            return basename($this->attachment_path);
        }
        return null;
    }

    /**
     * Get the attachment size (if available).
     */
    public function getAttachmentSizeAttribute(): ?string
    {
        if ($this->attachment_path && file_exists(storage_path('app/public/' . $this->attachment_path))) {
            $bytes = filesize(storage_path('app/public/' . $this->attachment_path));
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return null;
    }

    /**
     * Check if the attachment is an image.
     */
    public function isImage(): bool
    {
        if ($this->attachment_path) {
            $extension = strtolower(pathinfo($this->attachment_path, PATHINFO_EXTENSION));
            return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
        }
        return false;
    }

    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        // Auto-generate slug when updating title
        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        // Auto-generate excerpt if not provided
        static::saving(function ($article) {
            if (empty($article->excerpt) && !empty($article->content)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 200);
            }
        });
    }

    /**
     * Generate a unique slug for the article.
     */
    protected static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the formatted tags as an array.
     */
    public function getTagsArrayAttribute()
    {
        if (empty($this->tags)) {
            return [];
        }

        return array_map('trim', explode(',', $this->tags));
    }

    /**
     * Set tags from an array.
     */
    public function setTagsFromArray(array $tags)
    {
        $this->tags = implode(', ', array_filter($tags));
    }

    /**
     * Get the reading time in minutes.
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $wordsPerMinute = 200; // Average reading speed

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get the helpful percentage.
     */
    public function getHelpfulPercentageAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        
        if ($total === 0) {
            return 0;
        }

        return round(($this->helpful_count / $total) * 100);
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Scope a query to only include draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('published', false);
    }

    /**
     * Scope a query to include articles by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search articles.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%")
              ->orWhere('tags', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }

    /**
     * Get the URL for the article.
     */
    public function getUrlAttribute()
    {
        return route('knowledge-base.show', $this->slug);
    }

    /**
     * Get the attachment URL if it exists.
     */
    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment_path) {
            return null;
        }

        return asset('storage/' . $this->attachment_path);
    }

    /**
     * Get the attachment filename.
     */
    public function getAttachmentFilenameAttribute()
    {
        if (!$this->attachment_path) {
            return null;
        }

        return basename($this->attachment_path);
    }

    /**
     * Check if the article has an attachment.
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the published status as a human-readable string.
     */
    public function getPublishedStatusAttribute()
    {
        return $this->published ? 'Publié' : 'Brouillon';
    }

    /**
     * Get the formatted creation date.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get the formatted update date.
     */
    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i');
    }

    /**
     * Check if the article was recently updated.
     */
    public function isRecentlyUpdated()
    {
        return $this->updated_at->diffInDays(now()) <= 7;
    }

    /**
     * Get a short excerpt for previews.
     */
    public function getShortExcerptAttribute()
    {
        return Str::limit($this->excerpt ?? strip_tags($this->content), 100);
    }
}
