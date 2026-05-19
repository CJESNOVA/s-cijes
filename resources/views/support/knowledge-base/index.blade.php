@extends('layouts.app')

@section('title', 'Base de Connaissances - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Base de Connaissances</h1>
            <p class="page-subtitle">Trouvez rapidement les réponses à vos questions</p>
        </div>
        <div class="header-right">
            @if(Auth::user()->role->titre === 'Administrateur' || Auth::user()->role->titre === 'Superviseur')
            <a href="{{ route('knowledge-base.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvel Article
            </a>
            @endif
            @if(Auth::user()->role->titre === 'Administrateur' || Auth::user()->role->titre === 'Superviseur')
            <a href="{{ route('knowledge-base.dashboard') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/>
                    <path d="M3 9h18"/>
                    <path d="M3 15h18"/>
                    <path d="M9 3v18"/>
                    <path d="M15 3v18"/>
                </svg>
                Dashboard
            </a>
            @endif
        </div>
    </div>
</header>

<!-- Search and Filters -->
<div class="kb-search-section">
    <div class="kb-search-container">
        <div class="kb-search-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input 
                type="text" 
                id="kbSearch" 
                class="kb-search-input" 
                placeholder="Rechercher un article..." 
                value="{{ $search }}"
                autocomplete="off"
            >
            <div id="searchResults" class="kb-search-results"></div>
        </div>
        
        <div class="kb-filters">
            <select name="category_id" class="kb-filter-select" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }} ({{ $category->articles_count }})
                </option>
                @endforeach
            </select>
            
            <select name="sort" class="kb-filter-select" onchange="this.form.submit()">
                <option value="updated_at" {{ $sort == 'updated_at' ? 'selected' : '' }}>Dernière mise à jour</option>
                <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Date de création</option>
                <option value="title" {{ $sort == 'title' ? 'selected' : '' }}>Titre</option>
                <option value="views_count" {{ $sort == 'views_count' ? 'selected' : '' }}>Plus vus</option>
                <option value="helpful_count" {{ $sort == 'helpful_count' ? 'selected' : '' }}>Plus utiles</option>
            </select>
            
            <select name="order" class="kb-filter-select" onchange="this.form.submit()">
                <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Décroissant</option>
                <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Croissant</option>
            </select>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="kb-main">
    <div class="kb-sidebar">
        <!-- Categories -->
        <div class="kb-section">
            <h3 class="kb-section-title">Catégories</h3>
            <div class="kb-categories">
                @foreach($categories as $category)
                <a href="{{ route('knowledge-base.index', ['category_id' => $category->id]) }}" 
                   class="kb-category-item {{ $category_id == $category->id ? 'active' : '' }}">
                    <div class="kb-category-icon" style="background-color: {{ $category->color }}20;">
                        @if($category->icon)
                        <i class="{{ $category->icon }}"></i>
                        @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        @endif
                    </div>
                    <div class="kb-category-info">
                        <div class="kb-category-name">{{ $category->name }}</div>
                        <div class="kb-category-count">{{ $category->articles_count }} articles</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Popular Articles -->
        <div class="kb-section">
            <h3 class="kb-section-title">Articles Populaires</h3>
            <div class="kb-popular">
                @foreach($popularArticles as $article)
                <a href="{{ route('knowledge-base.show', $article) }}" class="kb-popular-item">
                    <div class="kb-popular-title">{{ $article->title }}</div>
                    <div class="kb-popular-stats">{{ $article->views_count }} vues</div>
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Recent Articles -->
        <div class="kb-section">
            <h3 class="kb-section-title">Articles Récents</h3>
            <div class="kb-recent">
                @foreach($recentArticles as $article)
                <a href="{{ route('knowledge-base.show', $article) }}" class="kb-recent-item">
                    <div class="kb-recent-title">{{ $article->title }}</div>
                    <div class="kb-recent-date">{{ $article->updated_at->diffForHumans() }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="kb-content">
        @if($search)
        <div class="kb-search-info">
            <p>Résultats pour "<strong>{{ $search }}</strong>" : {{ $articles->total() }} article(s)</p>
        </div>
        @endif
        
        @if($articles->count() > 0)
        <div class="kb-articles">
            @foreach($articles as $article)
            <article class="kb-article">
                <div class="kb-article-header">
                    <div class="kb-article-category">
                        <span class="kb-category-badge" style="background-color: {{ $article->category->color }}20; color: {{ $article->category->color }};">
                            {{ $article->category->name }}
                        </span>
                    </div>
                    <div class="kb-article-meta">
                        <span class="kb-article-views">{{ $article->views_count }} vues</span>
                        <span class="kb-article-date">{{ $article->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                <div class="kb-article-content">
                    <h2 class="kb-article-title">
                        <a href="{{ route('knowledge-base.show', $article) }}">{{ $article->title }}</a>
                    </h2>
                    <p class="kb-article-excerpt">{{ $article->excerpt }}</p>
                    
                    @if($article->tags)
                    <div class="kb-article-tags">
                        @foreach(explode(',', $article->tags) as $tag)
                        <span class="kb-tag">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <div class="kb-article-footer">
                    <div class="kb-article-author">
                        <div class="kb-author-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 10px;">
                            {{ strtoupper(substr($article->author->nom, 0, 1)) }}{{ strtoupper(substr($article->author->prenom, 0, 1)) }}
                        </div>
                        <div class="kb-author-info">
                            <div class="kb-author-name">{{ $article->author->nom }} {{ $article->author->prenom }}</div>
                            <div class="kb-author-role">{{ $article->author->role->titre }}</div>
                        </div>
                    </div>
                    
                    <div class="kb-article-stats">
                        @if($article->helpful_count > 0)
                        <span class="kb-helpful">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                            </svg>
                            {{ $article->helpful_count }}
                        </span>
                        @endif
                        <span class="kb-reading-time">{{ $article->reading_time }} min</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="kb-pagination">
            {{ $articles->links() }}
        </div>
        @else
        <div class="kb-empty">
            <div class="kb-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <h3 class="kb-empty-title">Aucun article trouvé</h3>
            <p class="kb-empty-description">
                @if($search)
                Aucun article ne correspond à votre recherche. Essayez avec d'autres mots-clés.
                @else
                Il n'y a aucun article dans cette catégorie pour le moment.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Styles -->
<style>

.kb-search-section {
    max-width: 1200px;
    margin: 0 auto 2rem;
    padding: 0 2rem;
}

.kb-search-container {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.kb-search-box {
    flex: 1;
    position: relative;
}

.kb-search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s;
}

.kb-search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.kb-search-box svg {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.kb-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: var(--shadow-lg);
    max-height: 400px;
    overflow-y: auto;
    z-index: 100;
    display: none;
}

.kb-filters {
    display: flex;
    gap: 0.5rem;
}

.kb-filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
}

.kb-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

.kb-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.kb-section {
    margin-bottom: 2rem;
}

.kb-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-categories {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kb-category-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    color: var(--gray-700);
    transition: all 0.2s;
}

.kb-category-item:hover {
    background: var(--gray-50);
    color: var(--primary);
}

.kb-category-item.active {
    background: var(--primary);
    color: white;
}

.kb-category-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.kb-category-item.active .kb-category-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.kb-category-info {
    flex: 1;
}

.kb-category-name {
    font-weight: 500;
    font-size: 0.875rem;
}

.kb-category-count {
    font-size: 0.75rem;
    opacity: 0.7;
}

.kb-articles {
    display: grid;
    gap: 1.5rem;
}

.kb-article {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: all 0.2s;
}

.kb-article:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.kb-article-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.kb-category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.kb-article-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.kb-article-title {
    margin-bottom: 0.75rem;
}

.kb-article-title a {
    text-decoration: none;
    color: var(--gray-900);
    font-size: 1.25rem;
    font-weight: 600;
}

.kb-article-title a:hover {
    color: var(--primary);
}

.kb-article-excerpt {
    color: var(--gray-600);
    margin-bottom: 1rem;
    line-height: 1.6;
}

.kb-article-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.kb-tag {
    padding: 0.25rem 0.5rem;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 4px;
    font-size: 0.75rem;
}

.kb-article-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.kb-article-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kb-author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.kb-author-name {
    font-weight: 500;
    font-size: 0.875rem;
}

.kb-author-role {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.kb-article-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.kb-helpful {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--success);
}

.kb-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.kb-empty-icon {
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.kb-empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-900);
}

.kb-empty-description {
    color: var(--gray-600);
}

@media (max-width: 1024px) {
    .kb-main {
        grid-template-columns: 1fr;
    }
    
    .kb-sidebar {
        position: static;
        order: 2;
    }
    
    .kb-content {
        order: 1;
    }
}

@media (max-width: 768px) {
    .kb-header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .kb-search-container {
        flex-direction: column;
    }
    
    .kb-filters {
        flex-wrap: wrap;
    }
    
    .kb-filter-select {
        flex: 1;
        min-width: 150px;
    }
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('kbSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;
    
    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/knowledge-base/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        });
        
        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
    
    function displaySearchResults(articles) {
        if (articles.length === 0) {
            searchResults.innerHTML = '<div class="kb-search-no-results">Aucun résultat trouvé</div>';
        } else {
            searchResults.innerHTML = articles.map(article => `
                <a href="/knowledge-base/${article.slug}" class="kb-search-result">
                    <div class="kb-search-result-title">${article.title}</div>
                    <div class="kb-search-result-excerpt">${article.excerpt}</div>
                    <div class="kb-search-result-category">${article.category.name}</div>
                </a>
            `).join('');
        }
        
        searchResults.style.display = 'block';
    }
});
</script>
@endsection
