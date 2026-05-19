@extends('layouts.app')

@section('title', $article->title . ' - Base de Connaissances - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">{{ $article->title }}</h1>
            <p class="page-subtitle">
                <span class="kb-category-badge" style="background-color: {{ $article->category->color }}20; color: {{ $article->category->color }};">
                    {{ $article->category->name }}
                </span>
                <span class="kb-meta-separator">·</span>
                <span class="kb-reading-time">{{ $article->reading_time }} min de lecture</span>
                <span class="kb-meta-separator">·</span>
                <span class="kb-views">{{ $article->views_count }} vues</span>
            </p>
        </div>
        <div class="header-right">
            @if(Auth::user()->role->titre === 'Administrateur' || Auth::user()->role->titre === 'Superviseur' || $article->author_id === Auth::id())
            <a href="{{ route('knowledge-base.edit', $article) }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Modifier
            </a>
            @endif
            <a href="{{ route('knowledge-base.index') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</header>

<!-- Article Content -->
<div class="kb-article-container">
    <div class="kb-main-content">
        <!-- Article Meta -->
        <div class="kb-article-meta">
            <div class="kb-author-info">
                <div class="kb-author-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                            {{ strtoupper(substr($article->author->nom, 0, 1)) }}{{ strtoupper(substr($article->author->prenom, 0, 1)) }}
                        </div>
                <div class="kb-author-details">
                    <div class="kb-author-name">{{ $article->author->nom }} {{ $article->author->prenom }}</div>
                    <div class="kb-author-role">{{ $article->author->role->titre }}</div>
                </div>
            </div>
            <div class="kb-article-dates">
                <div class="kb-date-item">
                    <strong>Créé le :</strong> {{ $article->created_at->format('d/m/Y à H:i') }}
                </div>
                <div class="kb-date-item">
                    <strong>Mis à jour le :</strong> {{ $article->updated_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <article class="kb-article-content">
            <div class="kb-article-body">
                {!! $article->content !!}
            </div>
            
            @if($article->tags)
            <div class="kb-article-tags">
                <h4>Tags</h4>
                <div class="kb-tags-list">
                    @foreach(explode(',', $article->tags) as $tag)
                    <span class="kb-tag">{{ trim($tag) }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($article->hasAttachment())
            <div class="kb-article-attachments">
                <h4>Fichier joint</h4>
                <div class="kb-attachments-grid">
                    @if($article->isImage())
                    <div class="kb-attachment-item image-attachment" data-full-src="{{ $article->attachment_url }}">
                        <img src="{{ $article->attachment_thumbnail_url }}" alt="{{ $article->attachment_name }}" class="kb-attachment-thumbnail">
                        <span class="kb-attachment-name">{{ $article->attachment_name }}</span>
                    </div>
                    @else
                    <a href="{{ $article->attachment_url }}" class="kb-attachment-item file-attachment" download="{{ $article->attachment_name }}">
                        <div class="kb-attachment-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <span class="kb-attachment-name">{{ $article->attachment_name }}</span>
                        <span class="kb-attachment-size">{{ $article->attachment_size }}</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </article>

        <!-- Article Feedback -->
        <div class="kb-article-feedback">
            <h4>Cet article vous a-t-il été utile ?</h4>
            <div class="kb-feedback-buttons">
                <button class="kb-feedback-btn kb-helpful-btn" data-article-id="{{ $article->id }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                    <span class="kb-feedback-text">Oui ({{ $article->helpful_count }})</span>
                </button>
                <button class="kb-feedback-btn kb-not-helpful-btn" data-article-id="{{ $article->id }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 15V9a3 3 0 0 1 3-3l4 9v11H5.72a2 2 0 0 1-2-1.7L2.34 15a2 2 0 0 1 2-2.3zM17 22h3a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-3"/>
                    </svg>
                    <span class="kb-feedback-text">Non ({{ $article->not_helpful_count }})</span>
                </button>
            </div>
            <div class="kb-feedback-message" style="display: none;">
                Merci pour votre feedback !
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="kb-sidebar">
        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
        <div class="kb-sidebar-section">
            <h3 class="kb-sidebar-title">Articles liés</h3>
            <div class="kb-related-articles">
                @foreach($relatedArticles as $related)
                <a href="{{ route('knowledge-base.show', $related) }}" class="kb-related-item">
                    <h4>{{ $related->title }}</h4>
                    <p>{{ $related->excerpt }}</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Similar Articles -->
        @if($similarArticles->count() > 0)
        <div class="kb-sidebar-section">
            <h3 class="kb-sidebar-title">Articles similaires</h3>
            <div class="kb-similar-articles">
                @foreach($similarArticles as $similar)
                <a href="{{ route('knowledge-base.show', $similar) }}" class="kb-similar-item">
                    <h4>{{ $similar->title }}</h4>
                    <p>{{ $similar->excerpt }}</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Back to Category -->
        <div class="kb-sidebar-section">
            <h3 class="kb-sidebar-title">Navigation</h3>
            <div class="kb-navigation">
                <a href="{{ route('knowledge-base.index', ['category_id' => $article->category_id]) }}" class="kb-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Voir tous les articles "{{ $article->category->name }}"
                </a>
                <a href="{{ route('knowledge-base.index') }}" class="kb-nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Retour à la base de connaissances
                </a>
            </div>
        </div>
    </aside>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="image-modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <img id="modalImage" src="" alt="Aperçu de l'image">
        <div class="modal-info">
            <h3 id="modalTitle"></h3>
            <p id="modalSize"></p>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.kb-article-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

.kb-main-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.kb-category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.kb-meta-separator {
    margin: 0 0.5rem;
    color: var(--gray-400);
}

.kb-reading-time,
.kb-views {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.kb-article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 2rem;
}

.kb-author-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kb-author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.kb-author-name {
    font-weight: 600;
    font-size: 0.875rem;
}

.kb-author-role {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.kb-article-dates {
    text-align: right;
}

.kb-date-item {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.kb-article-content {
    margin-bottom: 2rem;
}

.kb-article-body {
    line-height: 1.8;
    color: var(--gray-800);
}

.kb-article-body h1,
.kb-article-body h2,
.kb-article-body h3,
.kb-article-body h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-article-body h1 { font-size: 2rem; }
.kb-article-body h2 { font-size: 1.75rem; }
.kb-article-body h3 { font-size: 1.5rem; }
.kb-article-body h4 { font-size: 1.25rem; }

.kb-article-body p {
    margin-bottom: 1rem;
}

.kb-article-body ul,
.kb-article-body ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.kb-article-body li {
    margin-bottom: 0.5rem;
}

.kb-article-body strong {
    color: var(--gray-900);
    font-weight: 600;
}

.kb-article-body code {
    background: var(--gray-100);
    padding: 0.125rem 0.25rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.kb-article-body pre {
    background: var(--gray-100);
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 1rem 0;
}

.kb-article-body blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1rem;
    margin: 1rem 0;
    color: var(--gray-600);
}

.kb-article-tags {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-200);
}

.kb-article-tags h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-tags-list {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.kb-tag {
    padding: 0.25rem 0.75rem;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.kb-article-attachment {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-200);
}

.kb-article-attachment h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-attachment-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--primary);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
}

.kb-attachment-link:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.kb-article-attachments {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-200);
}

.kb-article-attachments h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-attachments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.kb-attachment-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.kb-attachment-item:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.image-attachment {
    cursor: pointer;
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.kb-attachment-thumbnail {
    width: 100%;
    height: 180px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-attachment:hover .kb-attachment-thumbnail {
    transform: scale(1.05);
}

.kb-attachment-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary);
    color: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.kb-attachment-name {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    color: var(--gray-800);
    margin: 0.5rem 1rem 0.25rem 1rem;
    word-break: break-word;
}

.kb-attachment-size {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin: 0 1rem 0.5rem 1rem;
}

.file-attachment {
    color: inherit;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
}

.file-attachment:hover {
    color: inherit;
    text-decoration: none;
}

.file-attachment .kb-attachment-icon {
    margin-bottom: 0.5rem;
}

/* Responsive pour KB */
@media (max-width: 768px) {
    .kb-attachments-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .kb-attachment-thumbnail {
        height: 120px;
    }
    
    .kb-attachment-icon {
        width: 36px;
        height: 36px;
    }
}

/* Modal Styles */
.image-modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-in;
}

.modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.close-modal {
    position: absolute;
    top: -40px;
    right: -40px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.2s;
}

.close-modal:hover {
    color: var(--primary);
}

#modalImage {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-info {
    margin-top: 1rem;
    text-align: center;
    color: white;
}

.modal-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}

.modal-info p {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.8;
}

.kb-article-feedback {
    padding: 2rem;
    background: var(--gray-50);
    border-radius: 12px;
    text-align: center;
}

.kb-article-feedback h4 {
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-feedback-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 1rem;
}

.kb-feedback-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: 2px solid var(--gray-200);
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.kb-helpful-btn:hover {
    border-color: var(--success);
    color: var(--success);
    background: var(--success);
    color: white;
}

.kb-not-helpful-btn:hover {
    border-color: var(--danger);
    color: var(--danger);
    background: var(--danger);
    color: white;
}

.kb-feedback-message {
    color: var(--success);
    font-weight: 500;
    animation: fadeIn 0.3s ease-in;
}

.kb-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.kb-sidebar-section {
    margin-bottom: 2rem;
}

.kb-sidebar-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.kb-related-articles,
.kb-similar-articles {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.kb-related-item,
.kb-similar-item {
    display: block;
    padding: 1rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.kb-related-item:hover,
.kb-similar-item:hover {
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.kb-related-item h4,
.kb-similar-item h4 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-900);
}

.kb-related-item p,
.kb-similar-item p {
    font-size: 0.75rem;
    color: var(--gray-600);
    line-height: 1.4;
}

.kb-navigation {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kb-nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    color: var(--gray-700);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.kb-nav-link:hover {
    background: var(--gray-50);
    color: var(--primary);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
    .kb-article-container {
        grid-template-columns: 1fr;
    }
    
    .kb-sidebar {
        position: static;
        order: 2;
    }
    
    .kb-main-content {
        order: 1;
    }
}

@media (max-width: 768px) {
    .kb-article-container {
        padding: 1rem;
    }
    
    .kb-main-content {
        padding: 1.5rem;
    }
    
    .kb-article-meta {
        flex-direction: column;
        gap: 1rem;
        text-align: left;
    }
    
    .kb-article-dates {
        text-align: left;
    }
    
    .kb-feedback-buttons {
        flex-direction: column;
    }
    
    .kb-feedback-btn {
        justify-content: center;
    }
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const helpfulBtn = document.querySelector('.kb-helpful-btn');
    const notHelpfulBtn = document.querySelector('.kb-not-helpful-btn');
    const feedbackMessage = document.querySelector('.kb-feedback-message');
    
    // Feedback buttons
    if (helpfulBtn) {
        helpfulBtn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            
            fetch(`/knowledge-base/${articleId}/helpful`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.querySelector('.kb-feedback-text').textContent = `Oui (${data.count})`;
                    this.style.background = 'var(--success)';
                    this.style.color = 'white';
                    this.style.borderColor = 'var(--success)';
                    this.disabled = true;
                    notHelpfulBtn.disabled = true;
                    feedbackMessage.style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
    
    if (notHelpfulBtn) {
        notHelpfulBtn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            
            fetch(`/knowledge-base/${articleId}/not-helpful`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.querySelector('.kb-feedback-text').textContent = `Non (${data.count})`;
                    this.style.background = 'var(--danger)';
                    this.style.color = 'white';
                    this.style.borderColor = 'var(--danger)';
                    this.disabled = true;
                    helpfulBtn.disabled = true;
                    feedbackMessage.style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Image Modal functionality
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalSize = document.getElementById('modalSize');
    const closeModal = document.querySelector('.close-modal');
    
    // Open modal when clicking on image attachment
    document.querySelectorAll('.image-attachment').forEach(item => {
        item.addEventListener('click', function() {
            const fullSrc = this.dataset.fullSrc;
            const fileName = this.querySelector('.kb-attachment-name').textContent;
            
            modalImg.src = fullSrc;
            modalTitle.textContent = fileName;
            modalSize.textContent = 'Cliquez sur l\'image pour fermer';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Close modal when clicking on close button
    if (closeModal) {
        closeModal.addEventListener('click', closeModalFunction);
    }
    
    // Close modal when clicking on the image
    modalImg.addEventListener('click', closeModalFunction);
    
    // Close modal when clicking outside the image
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalFunction();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModalFunction();
        }
    });
    
    function closeModalFunction() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});
</script>
@endsection
