<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KnowledgeBaseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the articles in the category.
     */
    public function articles()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }

    /**
     * Get the published articles in the category.
     */
    public function publishedArticles()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id')
            ->where('published', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });

        // Auto-generate slug when updating name
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });
    }

    /**
     * Generate a unique slug for the category.
     */
    protected static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the URL for the category.
     */
    public function getUrlAttribute()
    {
        return route('knowledge-base.index', ['category_id' => $this->id]);
    }

    /**
     * Get the formatted color for display.
     */
    public function getFormattedColorAttribute()
    {
        return $this->color ?? '#3B82F6';
    }

    /**
     * Get the icon HTML if it exists.
     */
    public function getIconHtmlAttribute()
    {
        if (!$this->icon) {
            return null;
        }

        return "<i class='{$this->icon}'></i>";
    }

    /**
     * Get the article count for the category.
     */
    public function getArticleCountAttribute()
    {
        return $this->articles()->where('published', true)->count();
    }

    /**
     * Get the total article count (including drafts).
     */
    public function getTotalArticleCountAttribute()
    {
        return $this->articles()->count();
    }

    /**
     * Check if the category has any articles.
     */
    public function hasArticles()
    {
        return $this->articles()->exists();
    }

    /**
     * Check if the category has any published articles.
     */
    public function hasPublishedArticles()
    {
        return $this->publishedArticles()->exists();
    }

    /**
     * Get the category statistics.
     */
    public function getStatsAttribute()
    {
        return [
            'total_articles' => $this->articles()->count(),
            'published_articles' => $this->publishedArticles()->count(),
            'draft_articles' => $this->articles()->where('published', false)->count(),
            'total_views' => $this->publishedArticles()->sum('views_count'),
        ];
    }

    /**
     * Get the most recent article in the category.
     */
    public function getLatestArticleAttribute()
    {
        return $this->publishedArticles()
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * Get the most popular article in the category.
     */
    public function getPopularArticleAttribute()
    {
        return $this->publishedArticles()
            ->orderBy('views_count', 'desc')
            ->first();
    }
}
