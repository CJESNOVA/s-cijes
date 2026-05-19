@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Base de Connaissances</h1>
            <p class="page-subtitle">Dashboard administratif</p>
        </div>
        <div class="header-right">
            <a href="{{ route('knowledge-base.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvel Article
            </a>
            <a href="{{ route('knowledge-base.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
                Voir la Base
            </a>
        </div>
    </div>
</header>

<!-- Stats Grid -->
<div class="stats-grid" data-testid="stats-grid">
    <div class="stat-card" data-testid="stat-articles">
        <div class="stat-header">
            <span class="stat-label">Total Articles</span>
            <div class="stat-icon articles">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10,9 9,9 8,9"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_articles'] }}</div>
        <div class="stat-change">{{ $stats['published_articles'] }} publiés</div>
    </div>

    <div class="stat-card" data-testid="stat-views">
        <div class="stat-header">
            <span class="stat-label">Total Vues</span>
            <div class="stat-icon views">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_views']) }}</div>
        <div class="stat-change">{{ $stats['total_helpful'] }} utiles</div>
    </div>

    <div class="stat-card" data-testid="stat-categories">
        <div class="stat-header">
            <span class="stat-label">Catégories</span>
            <div class="stat-icon categories">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['categories_count'] }}</div>
        <div class="stat-change">Actives</div>
    </div>

    <div class="stat-card" data-testid="stat-drafts">
        <div class="stat-header">
            <span class="stat-label">Brouillons</span>
            <div class="stat-icon drafts">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['draft_articles'] }}</div>
        <div class="stat-change">En attente</div>
    </div>
</div>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Popular Articles -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Articles Populaires</h3>
            <a href="{{ route('knowledge-base.index', ['sort' => 'views_count', 'order' => 'desc']) }}" class="link-small">Voir tout</a>
        </div>
        <div class="card-content">
            @forelse($popularArticles as $article)
            <div class="kb-article-item">
                <div class="kb-article-info">
                    <h4 class="kb-article-title">
                        <a href="{{ route('knowledge-base.show', $article) }}">{{ $article->title }}</a>
                    </h4>
                    <div class="kb-article-meta">
                        <span class="kb-article-views">{{ $article->views_count }} vues</span>
                        <span class="kb-article-helpful">{{ $article->helpful_count }} utiles</span>
                        <span class="kb-article-category">{{ $article->category->name ?? 'Non catégorisé' }}</span>
                    </div>
                </div>
                <div class="kb-article-stats">
                    <div class="stat-bar">
                        <div class="stat-fill" style="width: {{ min(100, ($article->views_count / max(1, $popularArticles->first()->views_count)) * 100) }}%;"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>Aucun article populaire pour le moment.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activité Récente</h3>
            <span class="badge badge-info">{{ $recentActivity->count() }} articles</span>
        </div>
        <div class="card-content">
            @forelse($recentActivity as $article)
            <div class="activity-item">
                <div class="activity-avatar">
                    <div class="user-avatar-small" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                        {{ strtoupper(substr($article->author->nom, 0, 1)) }}{{ strtoupper(substr($article->author->prenom, 0, 1)) }}
                    </div>
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        <a href="{{ route('knowledge-base.show', $article) }}">{{ $article->title }}</a>
                        @if(!$article->published)
                        <span class="badge badge-warning">Brouillon</span>
                        @endif
                    </div>
                    <div class="activity-meta">
                        <span class="activity-author">{{ $article->author->nom }} {{ $article->author->prenom }}</span>
                        <span class="activity-date">{{ $article->updated_at->format('d/m/Y à H:i') }}</span>
                        @if($article->category)
                        <span class="activity-category">{{ $article->category->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>Aucune activité récente.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Category Stats -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Répartition par Catégorie</h3>
            <a href="{{ route('knowledge-base.index') }}" class="link-small">Gérer</a>
        </div>
        <div class="card-content">
            @forelse($categoryStats as $category)
            <div class="category-stat-item">
                <div class="category-info">
                    <span class="category-name">{{ $category->name }}</span>
                    <span class="category-count">{{ $category->articles_count }} articles</span>
                </div>
                <div class="category-bar">
                    <div class="category-fill" style="width: {{ min(100, ($category->articles_count / max(1, $categoryStats->first()->articles_count)) * 100) }}%; background: {{ $category->color ?? '#3B82F6' }};"></div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>Aucune catégorie trouvée.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Actions Rapides</h3>
        </div>
        <div class="card-content">
            <div class="quick-actions">
                <a href="{{ route('knowledge-base.create') }}" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Créer un Article</span>
                </a>
                <a href="{{ route('knowledge-base.index') }}" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>Rechercher</span>
                </a>
                @if(in_array(Auth::user()->role->titre, ['Administrateur']))
                <a href="#" class="quick-action-btn" onclick="alert('Fonctionnalité à venir')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20V10"/>
                        <path d="M18 20V4"/>
                        <path d="M6 20v-4"/>
                    </svg>
                    <span>Statistiques</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Dashboard Styles */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.card-content {
    padding: 1.5rem;
}

/* KB Article Items */
.kb-article-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.kb-article-item:last-child {
    border-bottom: none;
}

.kb-article-info {
    flex: 1;
}

.kb-article-title {
    margin: 0 0 0.5rem 0;
}

.kb-article-title a {
    color: #111827;
    text-decoration: none;
    font-weight: 500;
}

.kb-article-title a:hover {
    color: #3b82f6;
}

.kb-article-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.kb-article-views,
.kb-article-helpful,
.kb-article-category {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kb-article-stats {
    width: 100px;
}

.stat-bar {
    height: 4px;
    background: #f3f4f6;
    border-radius: 2px;
    overflow: hidden;
}

.stat-fill {
    height: 100%;
    background: #3b82f6;
    transition: width 0.3s ease;
}

/* Activity Items */
.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-avatar {
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    margin: 0 0 0.25rem 0;
}

.activity-title a {
    color: #111827;
    text-decoration: none;
    font-weight: 500;
}

.activity-title a:hover {
    color: #3b82f6;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.activity-author,
.activity-date,
.activity-category {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Category Stats */
.category-stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.category-stat-item:last-child {
    border-bottom: none;
}

.category-info {
    flex: 1;
}

.category-name {
    font-weight: 500;
    color: #111827;
    display: block;
    margin-bottom: 0.25rem;
}

.category-count {
    font-size: 0.875rem;
    color: #6b7280;
}

.category-bar {
    width: 60px;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.category-fill {
    height: 100%;
    transition: width 0.3s ease;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s ease;
}

.quick-action-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #111827;
}

.quick-action-btn svg {
    flex-shrink: 0;
}

.quick-action-btn span {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state p {
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .kb-article-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .kb-article-stats {
        width: 100%;
    }
    
    .activity-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .category-stat-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .category-bar {
        width: 100%;
    }
}
</style>
@endpush
