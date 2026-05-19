@extends('layouts.app')

@section('title', 'Modifier un article - Base de Connaissances - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Modifier un article</h1>
            <p class="page-subtitle">Mettez à jour le contenu de l'article</p>
        </div>
        <div class="header-right">
            <a href="{{ route('knowledge-base.show', $article) }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                Voir l'article
            </a>
            <a href="{{ route('knowledge-base.index') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</header>

<!-- Form Container -->
<div class="kb-form-container">
    <div class="kb-form-card">
        <form action="{{ route('knowledge-base.update', $article) }}" method="POST" enctype="multipart/form-data" class="kb-article-form">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="kb-form-section">
                <h2 class="kb-section-title">Informations de base</h2>
                
                <div class="kb-form-group">
                    <label for="title" class="kb-form-label">Titre de l'article *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="kb-form-input @error('title') kb-invalid @enderror" 
                        value="{{ old('title', $article->title) }}"
                        placeholder="Entrez un titre clair et descriptif"
                        required
                    >
                    @error('title')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="kb-form-group">
                    <label for="category_id" class="kb-form-label">Catégorie *</label>
                    <select id="category_id" name="category_id" class="kb-form-select @error('category_id') kb-invalid @enderror" required>
                        <option value="">Sélectionnez une catégorie</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="kb-form-group">
                    <label for="excerpt" class="kb-form-label">Extrait (résumé)</label>
                    <textarea 
                        id="excerpt" 
                        name="excerpt" 
                        class="kb-form-textarea @error('excerpt') kb-invalid @enderror" 
                        rows="3"
                        placeholder="Brève description de l'article (max 500 caractères)"
                    >{{ old('excerpt', $article->excerpt) }}</textarea>
                    <div class="kb-form-help">
                        Laissez vide pour générer automatiquement à partir du contenu
                    </div>
                    @error('excerpt')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="kb-form-group">
                    <label for="tags" class="kb-form-label">Tags</label>
                    <input 
                        type="text" 
                        id="tags" 
                        name="tags" 
                        class="kb-form-input @error('tags') kb-invalid @enderror" 
                        value="{{ old('tags', $article->tags) }}"
                        placeholder="séparez les tags par des virgules (ex: ticket, support, guide)"
                    >
                    <div class="kb-form-help">
                        Les tags aident à la recherche et la classification
                    </div>
                    @error('tags')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Content -->
            <div class="kb-form-section">
                <h2 class="kb-section-title">Contenu de l'article</h2>
                
                <div class="kb-form-group">
                    <label for="content" class="kb-form-label">Contenu *</label>
                    <div class="kb-editor-toolbar">
                        <div class="kb-editor-group">
                            <button type="button" class="kb-editor-btn" data-command="bold" title="Gras">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                                    <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                                </svg>
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="italic" title="Italique">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="4" x2="10" y2="4"/>
                                    <line x1="14" y1="20" x2="5" y2="20"/>
                                    <line x1="15" y1="4" x2="9" y2="20"/>
                                </svg>
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="underline" title="Souligné">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"/>
                                    <line x1="4" y1="21" x2="20" y2="21"/>
                                </svg>
                            </button>
                        </div>
                        <div class="kb-editor-group">
                            <button type="button" class="kb-editor-btn" data-command="formatBlock" data-value="h2" title="Titre 2">
                                H2
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="formatBlock" data-value="h3" title="Titre 3">
                                H3
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="formatBlock" data-value="h4" title="Titre 4">
                                H4
                            </button>
                        </div>
                        <div class="kb-editor-group">
                            <button type="button" class="kb-editor-btn" data-command="insertUnorderedList" title="Liste à puces">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"/>
                                    <line x1="8" y1="12" x2="21" y2="12"/>
                                    <line x1="8" y1="18" x2="21" y2="18"/>
                                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                                </svg>
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="insertOrderedList" title="Liste numérotée">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="10" y1="6" x2="21" y2="6"/>
                                    <line x1="10" y1="12" x2="21" y2="12"/>
                                    <line x1="10" y1="18" x2="21" y2="18"/>
                                    <path d="M4 6h1v4"/>
                                    <path d="M4 10h2"/>
                                    <path d="M6 18H2c0-1 2-2 2-3s-1-1.5-2-1"/>
                                </svg>
                            </button>
                        </div>
                        <div class="kb-editor-group">
                            <button type="button" class="kb-editor-btn" data-command="createLink" title="Lien">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                            </button>
                            <button type="button" class="kb-editor-btn" data-command="formatBlock" data-value="blockquote" title="Citation">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21c3 0 7-1 7-8V5c0-1-1-2-2-2s-2 1-2 2v8c0 2-1 3-3 3"/>
                                    <path d="M14 21c3 0 7-1 7-8V5c0-1-1-2-2-2s-2 1-2 2v8c0 2-1 3-3 3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div 
                        id="content" 
                        contenteditable="true" 
                        class="kb-form-editor @error('content') kb-invalid @enderror"
                        data-placeholder="Rédigez le contenu de votre article ici..."
                    >{{ old('content', $article->content) }}</div>
                    <textarea name="content" id="content-hidden" style="display: none;">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Attachments -->
            <div class="kb-form-section">
                <h2 class="kb-section-title">Fichier joint</h2>
                
                @if($article->hasAttachment())
                <div class="kb-current-attachment">
                    <h4>Fichier actuel</h4>
                    <div class="kb-attachment-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <div class="kb-attachment-details">
                            <div class="kb-attachment-name">{{ $article->attachment_filename }}</div>
                            <a href="{{ $article->attachment_url }}" class="kb-attachment-link" target="_blank">
                                Télécharger
                            </a>
                        </div>
                        <div class="kb-attachment-actions">
                            <label class="kb-replace-label">
                                <input type="checkbox" name="remove_attachment" value="1">
                                <span>Supprimer ce fichier</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="kb-form-group">
                    <label for="attachment" class="kb-form-label">
                        {{ $article->hasAttachment() ? 'Remplacer le fichier' : 'Téléverser un fichier' }}
                    </label>
                    <div class="kb-file-upload">
                        <input type="file" id="attachment" name="attachment" class="kb-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif">
                        <div class="kb-file-dropzone">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17,8 12,3 7,8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p><strong>Cliquez pour téléverser</strong> ou glissez-déposez ici</p>
                            <p class="kb-file-help">PDF, DOC, XLS, PPT, JPG, PNG (Max 10MB)</p>
                        </div>
                        <div class="kb-file-preview" style="display: none;">
                            <div class="kb-file-info">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <span class="kb-file-name"></span>
                                <button type="button" class="kb-file-remove">×</button>
                            </div>
                        </div>
                    </div>
                    @error('attachment')
                        <div class="kb-form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Publication -->
            <div class="kb-form-section">
                <h2 class="kb-section-title">Publication</h2>
                
                <div class="kb-form-group">
                    <div class="kb-checkbox-group">
                        <label class="kb-checkbox-label">
                            <input type="checkbox" name="published" value="1" {{ old('published', $article->published) ? 'checked' : '' }}>
                            <span class="kb-checkbox-custom"></span>
                            <span class="kb-checkbox-text">Publier immédiatement</span>
                        </label>
                        <div class="kb-form-help">
                            Si non coché, l'article sera sauvegardé comme brouillon
                        </div>
                    </div>
                </div>
                
                <div class="kb-form-group">
                    <div class="kb-article-info">
                        <div class="kb-info-item">
                            <strong>Créé le :</strong> {{ $article->created_at->format('d/m/Y à H:i') }}
                        </div>
                        <div class="kb-info-item">
                            <strong>Auteur :</strong> {{ $article->author->nom }} {{ $article->author->prenom }}
                        </div>
                        <div class="kb-info-item">
                            <strong>Dernière mise à jour :</strong> {{ $article->updated_at->format('d/m/Y à H:i') }}
                        </div>
                        <div class="kb-info-item">
                            <strong>Vues :</strong> {{ $article->views_count }}
                        </div>
                        <div class="kb-info-item">
                            <strong>Slug :</strong> {{ $article->slug }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="kb-form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Mettre à jour l'article
                </button>
                <a href="{{ route('knowledge-base.show', $article) }}" class="btn btn-outline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Styles -->
<style>
.kb-form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.kb-form-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.kb-form-section {
    padding: 2rem;
    border-bottom: 1px solid var(--gray-200);
}

.kb-form-section:last-child {
    border-bottom: none;
}

.kb-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--gray-900);
}

.kb-form-group {
    margin-bottom: 1.5rem;
}

.kb-form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.kb-form-input,
.kb-form-select,
.kb-form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.kb-form-input:focus,
.kb-form-select:focus,
.kb-form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.kb-form-input.kb-invalid,
.kb-form-select.kb-invalid,
.kb-form-textarea.kb-invalid {
    border-color: var(--danger);
}

.kb-form-textarea {
    resize: vertical;
    min-height: 100px;
}

.kb-form-help {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.kb-form-error {
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.kb-editor-toolbar {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem;
    background: var(--gray-50);
    border: 2px solid var(--gray-200);
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    flex-wrap: wrap;
}

.kb-editor-group {
    display: flex;
    gap: 0.25rem;
    padding-right: 0.5rem;
    border-right: 1px solid var(--gray-300);
}

.kb-editor-group:last-child {
    border-right: none;
}

.kb-editor-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.75rem;
    font-weight: 500;
}

.kb-editor-btn:hover {
    background: var(--gray-50);
    border-color: var(--primary);
}

.kb-editor-btn:active {
    background: var(--primary);
    color: white;
}

.kb-form-editor {
    width: 100%;
    min-height: 400px;
    padding: 1rem;
    border: 2px solid var(--gray-200);
    border-top: none;
    border-radius: 0 0 8px 8px;
    font-size: 0.875rem;
    line-height: 1.6;
    background: white;
    transition: all 0.2s;
}

.kb-form-editor:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.kb-form-editor.kb-invalid {
    border-color: var(--danger);
}

.kb-form-editor[contenteditable=true]:empty:before {
    content: attr(data-placeholder);
    color: var(--gray-400);
}

.kb-form-editor h1,
.kb-form-editor h2,
.kb-form-editor h3,
.kb-form-editor h4 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: var(--gray-900);
}

.kb-form-editor p {
    margin-bottom: 1rem;
}

.kb-form-editor ul,
.kb-form-editor ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.kb-form-editor blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1rem;
    margin: 1rem 0;
    color: var(--gray-600);
    font-style: italic;
}

.kb-current-attachment {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 8px;
    border: 1px solid var(--gray-200);
}

.kb-current-attachment h4 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-700);
}

.kb-attachment-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.kb-attachment-details {
    flex: 1;
}

.kb-attachment-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.kb-attachment-link {
    font-size: 0.75rem;
    color: var(--primary);
    text-decoration: none;
}

.kb-attachment-link:hover {
    text-decoration: underline;
}

.kb-attachment-actions {
    display: flex;
    align-items: center;
}

.kb-replace-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--danger);
    cursor: pointer;
}

.kb-replace-label input[type="checkbox"] {
    margin: 0;
}

.kb-file-upload {
    position: relative;
}

.kb-file-input {
    display: none;
}

.kb-file-dropzone {
    border: 2px dashed var(--gray-300);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.kb-file-dropzone:hover {
    border-color: var(--primary);
    background: var(--gray-50);
}

.kb-file-dropzone svg {
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.kb-file-dropzone p {
    margin: 0.5rem 0;
    color: var(--gray-600);
}

.kb-file-help {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.kb-file-preview {
    border: 2px solid var(--primary);
    border-radius: 8px;
    padding: 1rem;
    background: var(--primary);
    color: white;
}

.kb-file-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kb-file-name {
    flex: 1;
    font-weight: 500;
}

.kb-file-remove {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.25rem;
    color: white;
    transition: all 0.2s;
}

.kb-file-remove:hover {
    background: rgba(255, 255, 255, 0.3);
}

.kb-checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.kb-checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 500;
    color: var(--gray-700);
}

.kb-checkbox-label input[type="checkbox"] {
    display: none;
}

.kb-checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid var(--gray-300);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
    margin-top: 2px;
}

.kb-checkbox-label input[type="checkbox"]:checked + .kb-checkbox-custom {
    background: var(--primary);
    border-color: var(--primary);
}

.kb-checkbox-label input[type="checkbox"]:checked + .kb-checkbox-custom::after {
    content: '×';
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.kb-checkbox-text {
    flex: 1;
}

.kb-article-info {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
}

.kb-info-item {
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    color: var(--gray-600);
}

.kb-info-item:last-child {
    margin-bottom: 0;
}

.kb-info-item strong {
    color: var(--gray-800);
}

.kb-form-actions {
    padding: 2rem;
    background: var(--gray-50);
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .kb-form-container {
        padding: 1rem;
    }
    
    .kb-form-section {
        padding: 1.5rem;
    }
    
    .kb-editor-toolbar {
        gap: 0.25rem;
    }
    
    .kb-editor-btn {
        width: 28px;
        height: 28px;
        font-size: 0.625rem;
    }
    
    .kb-form-actions {
        flex-direction: column;
    }
    
    .kb-form-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .kb-attachment-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('content');
    const hiddenContent = document.getElementById('content-hidden');
    const fileInput = document.getElementById('attachment');
    const dropzone = document.querySelector('.kb-file-dropzone');
    const filePreview = document.querySelector('.kb-file-preview');
    const fileName = document.querySelector('.kb-file-name');
    const fileRemove = document.querySelector('.kb-file-remove');
    
    // Editor toolbar
    const editorButtons = document.querySelectorAll('.kb-editor-btn');
    editorButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const command = this.dataset.command;
            const value = this.dataset.value || null;
            
            if (command === 'createLink') {
                const url = prompt('Entrez l\'URL du lien:');
                if (url) {
                    document.execCommand(command, false, url);
                }
            } else {
                document.execCommand(command, false, value);
            }
            
            editor.focus();
            updateHiddenContent();
        });
    });
    
    // Update hidden content on editor change
    function updateHiddenContent() {
        hiddenContent.value = editor.innerHTML;
    }
    
    editor.addEventListener('input', updateHiddenContent);
    editor.addEventListener('paste', function(e) {
        e.preventDefault();
        const text = e.clipboardData.getData('text/plain');
        document.execCommand('insertText', false, text);
        updateHiddenContent();
    });
    
    // File upload
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            displayFilePreview(file);
        }
    });
    
    dropzone.addEventListener('click', function() {
        fileInput.click();
    });
    
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--primary)';
        this.style.background = 'var(--gray-50)';
    });
    
    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--gray-300)';
        this.style.background = 'transparent';
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--gray-300)';
        this.style.background = 'transparent';
        
        const file = e.dataTransfer.files[0];
        if (file) {
            fileInput.files = e.dataTransfer.files;
            displayFilePreview(file);
        }
    });
    
    function displayFilePreview(file) {
        fileName.textContent = file.name;
        dropzone.style.display = 'none';
        filePreview.style.display = 'block';
    }
    
    fileRemove.addEventListener('click', function() {
        fileInput.value = '';
        dropzone.style.display = 'block';
        filePreview.style.display = 'none';
    });
    
    // Auto-generate excerpt
    const titleInput = document.getElementById('title');
    const excerptTextarea = document.getElementById('excerpt');
    
    function generateExcerpt() {
        if (!excerptTextarea.value && editor.innerHTML) {
            const text = editor.innerHTML.replace(/<[^>]*>/g, '').substring(0, 200);
            excerptTextarea.value = text;
        }
    }
    
    titleInput.addEventListener('blur', generateExcerpt);
    editor.addEventListener('blur', generateExcerpt);
    
    // Initialize
    updateHiddenContent();
});
</script>
@endsection
