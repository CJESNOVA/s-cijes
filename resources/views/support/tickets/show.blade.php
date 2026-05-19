@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">{{ $ticket->reference }} - {{ $ticket->titre }}</h1>
            <p class="page-subtitle">Détails et suivi du ticket de support</p>
        </div>
        <div class="header-right">
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary" data-testid="back-to-list-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 14 4 9 9 4"/>
                    <path d="M20 20v-7a4 4 0 0 0-4-4H4"/>
                </svg>
                Retour à la liste
            </a>
            @if(auth()->user()->role->titre !== 'Demandeur')
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <span class="text-xs font-bold text-gray-500 uppercase">Changer l'état :</span>
                    
                    <form action="{{ route('tickets.status', $ticket) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        
                        <select name="statut_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach($statuts as $statut)
                                <option value="{{ $statut->id }}" {{ $ticket->statut_id == $statut->id ? 'selected' : '' }}>
                                    {{ $statut->nom }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-semibold hover:bg-blue-700 transition">
                            Appliquer
                        </button>
                    </form>
                </div>
            @endif
            <button class="btn btn-primary" data-testid="resolve-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Marquer comme résolu
            </button>
            <button class="icon-button" data-testid="notifications-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notification-badge">3</span>
            </button>
        </div>
    </div>
</header>

<!-- Ticket Content Layout -->
<div class="ticket-detail-layout">
    <!-- Left: Conversation Thread -->
    <div class="ticket-conversation" data-testid="ticket-conversation">
        <!-- Message from User -->
        <div class="message message-user" data-testid="message-user">
            <div class="message-avatar">
                <div class="user-avatar-large" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                    {{ strtoupper(substr($ticket->user->nom, 0, 1)) }}{{ strtoupper(substr($ticket->user->prenom, 0, 1)) }}
                </div>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-author">{{ $ticket->user->nom }} {{ $ticket->user->prenom }}</span>
                    <span class="message-time">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <div class="message-body">
                    <p>{{ $ticket->description }}</p>
                </div>
                @if($ticket->fichiers->count() > 0)
            <div class="kb-article-attachments">
                <h4>Fichiers joints</h4>
                <div class="kb-attachments-grid">
                    @foreach($ticket->fichiers as $fichier)
                        @if($fichier->isImage())
                        <div class="kb-attachment-item image-attachment" data-full-src="{{ $fichier->url }}" onclick="openImageModal('{{ $fichier->url }}', '{{ $fichier->nom }}')">
                            <img src="{{ $fichier->url }}" alt="{{ $fichier->nom }}" class="kb-attachment-thumbnail">
                            <span class="kb-attachment-name">{{ $fichier->nom }}</span>
                        </div>
                        @else
                        <a href="{{ $fichier->url }}" class="kb-attachment-item file-attachment" download="{{ $fichier->nom }}">
                            <div class="kb-attachment-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <span class="kb-attachment-name">{{ $fichier->nom }}</span>
                            <span class="kb-attachment-size">{{ $fichier->taille_formatee }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
            </div>
        </div>

        <!-- Fil de discussion -->
        <div class="space-y-6">
            @foreach($ticket->messages as $message)
                {{-- On cache les notes internes aux utilisateurs qui n'ont pas le droit de les voir --}}
                @if(!$message->interne || auth()->user()->role->titre !== 'Demandeur')
                    <div class="message {{ $message->user_id === auth()->id() ? 'message-self' : 'message-other' }}" data-testid="message-{{ $message->user_id === auth()->id() ? 'self' : 'other' }}">
                        <div class="message-avatar">
                            <div class="user-avatar-large" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                {{ strtoupper(substr($message->user->nom, 0, 1)) }}{{ strtoupper(substr($message->user->prenom, 0, 1)) }}
                            </div>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-author">{{ $message->user->nom }} {{ $message->user->prenom }}</span>
                                <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="message-body">
                                <p>{{ $message->message }}</p>
                            </div>
                            @if($message->fichiers && $message->fichiers->count() > 0)
                            <div class="message-attachments">
                                @foreach($message->fichiers as $fichier)
                                    @if($fichier->isImage())
                                    <div class="kb-attachment-item image-attachment" data-full-src="{{ $fichier->url }}" onclick="openImageModal('{{ $fichier->url }}', '{{ $fichier->nom }}')">
                                        <img src="{{ $fichier->url }}" alt="{{ $fichier->nom }}" class="kb-attachment-thumbnail">
                                        <span class="kb-attachment-name">{{ $fichier->nom }}</span>
                                    </div>
                                    @else
                                    <a href="{{ $fichier->url }}" class="kb-attachment-item file-attachment" download="{{ $fichier->nom }}">
                                        <div class="kb-attachment-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                        </div>
                                        <span class="kb-attachment-name">{{ $fichier->nom }}</span>
                                        <span class="kb-attachment-size">{{ $fichier->taille_formatee }}</span>
                                    </a>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Reply Box -->
        <div class="reply-box" data-testid="reply-box">
            <div class="reply-toggle">
                @if(auth()->user()->role->titre !== 'Demandeur')
                    <label class="toggle-switch">
                        <input type="checkbox" id="internal-note-toggle" name="interne" value="1" data-testid="internal-note-toggle">
                        <span class="toggle-slider"></span>
                    </label>
                    <label for="internal-note-toggle" class="toggle-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        </svg>
                        Note interne (visible uniquement par les techniciens)
                    </label>
                @else
                    <span></span>
                @endif
            </div>
            <form action="{{ route('tickets.messages.store', $ticket) }}" method="POST">
                @csrf
                <textarea name="message" class="reply-textarea" placeholder="Écrire une réponse..." rows="4" data-testid="reply-textarea" required></textarea>
                <div class="reply-actions">
                    <button type="button" class="btn btn-icon" data-testid="attach-file-button" title="Joindre un fichier">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                        </svg>
                    </button>
                    <button type="submit" class="btn btn-primary" data-testid="send-reply-button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Sidebar -->
    <aside class="ticket-sidebar" data-testid="ticket-sidebar">
        <!-- User Info -->
        <div class="sidebar-card" data-testid="user-info-card">
            <h3 class="sidebar-card-title">Utilisateur</h3>
            <div class="user-info-detail">
                <div class="user-avatar-large" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 24px;">
                    {{ strtoupper(substr($ticket->user->nom, 0, 1)) }}{{ strtoupper(substr($ticket->user->prenom, 0, 1)) }}
                </div>
                <div class="user-detail-text">
                    <div class="user-detail-name">{{ $ticket->user->nom }} {{ $ticket->user->prenom }}</div>
                    <div class="user-detail-email">{{ $ticket->user->email }}</div>
                    <div class="user-detail-phone">{{ $ticket->user->telephone ?? 'Non spécifié' }}</div>
                </div>
            </div>
        </div>

        <!-- Platform & Module -->
        <div class="sidebar-card" data-testid="platform-info-card">
            <h3 class="sidebar-card-title">Source du ticket</h3>
            <div class="platform-info">
                <div class="platform-logo">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563EB 0%, #1e40AF 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        {{ substr($ticket->plateforme->nom, 0, 2) }}
                    </div>
                </div>
                <div class="platform-details">
                    <div class="platform-name">{{ $ticket->plateforme->nom }}</div>
                    <div class="platform-module">Module: {{ $ticket->module->nom }}</div>
                    <div class="platform-sso">SSO Login: {{ $ticket->user->email }}</div>
                </div>
            </div>
        </div>

        <!-- Assigned Technician -->
        <div class="sidebar-card" data-testid="assigned-card">
            <h3 class="sidebar-card-title">Assigné à</h3>
            <div class="assigned-info">
                @if($ticket->technicien)
                    <div class="user-avatar-medium" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px;">
                        {{ strtoupper(substr($ticket->technicien->nom, 0, 1)) }}{{ strtoupper(substr($ticket->technicien->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <div class="assigned-name">{{ $ticket->technicien->nom }} {{ $ticket->technicien->prenom }}</div>
                        <div class="assigned-role">{{ $ticket->technicien->role->titre }}</div>
                    </div>
                @else
                    <span class="text-amber-600 font-bold animate-pulse">En attente d'agent</span>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="sidebar-card" data-testid="timeline-card">
            <div class="timeline-header">
                <h3 class="sidebar-card-title">Historique</h3>
                <a href="{{ route('tickets.history', $ticket) }}" class="link-small" data-testid="view-full-history">
                    Voir tout l'historique
                </a>
            </div>
            <div class="timeline">
                <!-- Ticket créé -->
                <div class="timeline-item" data-testid="timeline-item">
                    <div class="timeline-dot" style="background: #2563EB;"></div>
                    <div class="timeline-content">
                        <div class="timeline-text">Ticket créé</div>
                        <div class="timeline-time">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                
                <!-- Messages -->
                @foreach($ticket->messages as $message)
                <div class="timeline-item" data-testid="timeline-item">
                    <div class="timeline-dot" style="background: {{ $message->interne ? '#D97706' : '#059669' }};"></div>
                    <div class="timeline-content">
                        <div class="timeline-text">
                            @if($message->interne)
                                Note interne ajoutée
                            @else
                                {{ $message->user->nom }} a répondu
                            @endif
                        </div>
                        <div class="timeline-time">{{ $message->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="sidebar-card" data-testid="actions-card">
            <h3 class="sidebar-card-title">Actions rapides</h3>
            <div class="quick-actions">
                @if(auth()->user()->role->titre !== 'Demandeur')
                <button class="quick-action-btn" data-testid="change-priority-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Changer la priorité
                </button>
                @endif
                @if(auth()->user()->role->titre !== 'Demandeur')
                <button class="quick-action-btn" data-testid="change-status-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    Changer le statut
                </button>
                @endif
                @if(in_array(auth()->user()->role->titre, ['Administrateur', 'Superviseur']))
                <button class="quick-action-btn" data-testid="merge-ticket-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Fusionner avec un autre ticket
                </button>
                @endif
                @if(in_array(auth()->user()->role->titre, ['Administrateur', 'Superviseur']))
                <button class="quick-action-btn danger" data-testid="delete-ticket-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Supprimer le ticket
                </button>
                @endif
            </div>
        </div>

        <!-- Satisfaction Section -->
        @if($ticket->statut->nom === 'Résolu' && auth()->user()->role->titre === 'Demandeur' && !$ticket->satisfaction)
        <div class="sidebar-card" data-testid="satisfaction-card">
            <h3 class="sidebar-card-title">Satisfaction</h3>
            <div class="satisfaction-form">
                <p class="satisfaction-intro">Comment évaluez-vous la résolution de ce ticket ?</p>
                
                <form id="satisfaction-form" action="{{ route('tickets.satisfaction', $ticket) }}" method="POST">
                    @csrf
                    
                    <!-- Étoiles de notation -->
                    <div class="star-rating" data-testid="star-rating">
                        <input type="radio" id="star5" name="note" value="5" required>
                        <label for="star5" class="star" title="Excellent">★</label>
                        
                        <input type="radio" id="star4" name="note" value="4">
                        <label for="star4" class="star" title="Très bon">★</label>
                        
                        <input type="radio" id="star3" name="note" value="3">
                        <label for="star3" class="star" title="Bon">★</label>
                        
                        <input type="radio" id="star2" name="note" value="2">
                        <label for="star2" class="star" title="Moyen">★</label>
                        
                        <input type="radio" id="star1" name="note" value="1">
                        <label for="star1" class="star" title="Mauvais">★</label>
                    </div>
                    
                    <!-- Commentaire optionnel -->
                    <div class="satisfaction-comment">
                        <label for="commentaire" class="satisfaction-label">Commentaire (optionnel)</label>
                        <textarea name="commentaire" id="commentaire" rows="3" 
                                  placeholder="Dites-nous en plus sur votre expérience..." 
                                  class="satisfaction-textarea"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary satisfaction-submit" data-testid="submit-satisfaction">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Envoyer mon feedback
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Satisfaction affichée -->
        @if($ticket->satisfaction)
        <div class="sidebar-card" data-testid="satisfaction-display-card">
            <h3 class="sidebar-card-title">Satisfaction enregistrée</h3>
            <div class="satisfaction-display">
                <div class="satisfaction-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $ticket->satisfaction->note ? 'filled' : 'empty' }}">★</span>
                    @endfor
                    <span class="satisfaction-score">{{ $ticket->satisfaction->note }}/5</span>
                </div>
                @if($ticket->satisfaction->commentaire)
                <div class="satisfaction-comment-display">
                    <p>{{ $ticket->satisfaction->commentaire }}</p>
                </div>
                @endif
                <div class="satisfaction-date">
                    <small>Enregistrée le {{ $ticket->satisfaction->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif
    </aside>
</div>

<!-- Styles pour l'entête et la vue détaillée -->
<style>
.header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.icon-button {
    background: white;
    border: 1px solid #e5e7eb;
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.icon-button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.125rem 0.375rem;
    border-radius: 9999px;
    min-width: 1.25rem;
    text-align: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-secondary {
    background: white;
    color: #374151;
    border-color: #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 0;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.breadcrumb-link {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-link:hover {
    color: #2563eb;
}

.breadcrumb-current {
    color: #1f2937;
    font-weight: 600;
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ticket-header-left {
    flex: 1;
}

.ticket-header-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.ticket-id-large {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
}

.ticket-title-large {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.ticket-header-actions {
    display: flex;
    gap: 0.75rem;
}

.ticket-detail-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
}

.ticket-conversation {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.message {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.message-user {
    justify-content: flex-start;
}

.message-technician {
    justify-content: flex-start;
}

.message-internal {
    justify-content: flex-start;
}

.message-avatar {
    flex-shrink: 0;
}

.user-avatar-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-bottom: 0.5rem;
}

.message-content {
    flex: 1;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.message-internal .message-content {
    background: #fef3c7;
    border-color: #f59e0b;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.message-author {
    font-weight: 600;
    color: #1f2937;
}

.message-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.message-badge {
    background: #2563eb;
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 500;
}

.internal-badge {
    background: #d97706;
}

.message-body {
    color: #374151;
    line-height: 1.5;
}

.message-attachments {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.attachment {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.attachment-size {
    color: #6b7280;
    font-size: 0.75rem;
}

.reply-box {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.reply-toggle {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin-right: 0.5rem;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e7eb;
    transition: 0.4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #2563eb;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.toggle-label {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
    cursor: pointer;
}

.reply-textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem;
    font-size: 0.875rem;
    resize: vertical;
    min-height: 100px;
}

.reply-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.ticket-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.sidebar-card-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
}

.user-info-detail {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-medium {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.user-detail-text {
    flex: 1;
}

.user-detail-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.user-detail-email {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.user-detail-phone {
    color: #6b7280;
    font-size: 0.875rem;
}

.platform-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.platform-logo {
    flex-shrink: 0;
}

.platform-details {
    flex: 1;
}

.platform-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.platform-module {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.platform-sso {
    color: #6b7280;
    font-size: 0.75rem;
}

.assigned-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.assigned-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.assigned-role {
    color: #6b7280;
    font-size: 0.875rem;
}

.unassigned-text {
    color: #9ca3af;
    font-style: italic;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.link-small {
    font-size: 0.75rem;
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}

.link-small:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.25rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-dot {
    position: absolute;
    left: -0.375rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 1rem;
}

.timeline-text {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.timeline-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
}

.quick-action-btn:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.quick-action-btn.danger {
    color: #dc2626;
    border-color: #fecaca;
}

.quick-action-btn.danger:hover {
    background: #fef2f2;
    border-color: #fca5a5;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-urgent {
    background: #fee2e2;
    color: #dc2626;
}

.badge-high {
    background: #fed7aa;
    color: #ea580c;
}

.badge-normal {
    background: #fef3c7;
    color: #d97706;
}

.badge-low {
    background: #dbeafe;
    color: #2563eb;
}

.badge-ouvert {
    background: #dbeafe;
    color: #2563eb;
}

.badge-en-cours {
    background: #fef3c7;
    color: #d97706;
}

.badge-resolu {
    background: #d1fae5;
    color: #059669;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-secondary {
    background: white;
    color: #374151;
    border-color: #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.btn-icon {
    background: white;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    padding: 0.5rem;
}

.btn-icon:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.internal-note-icon {
    width: 16px;
    height: 16px;
    color: #d97706;
}

/* Styles pour la messagerie */
.space-y-6 > * + * {
    margin-top: 1.5rem;
}

/* Styles pour les pièces jointes - Identiques à la base de connaissances */
.kb-article-attachments {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.kb-article-attachments h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: #111827;
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
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.kb-attachment-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.kb-attachment-name {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
}

/* Styles pour le formulaire de satisfaction */
.satisfaction-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.satisfaction-intro {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
    text-align: center;
}

.star-rating {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
    margin: 1rem 0;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating .star {
    font-size: 2rem;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating .star:hover,
.star-rating .star:hover ~ .star {
    color: #fbbf24;
}

.star-rating input[type="radio"]:checked ~ .star {
    color: #fbbf24;
}

.satisfaction-comment {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.satisfaction-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.satisfaction-textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem;
    font-size: 0.875rem;
    resize: vertical;
    min-height: 80px;
}

.satisfaction-textarea:focus {
    outline: none;
    border-color: #2563eb;
    ring: 1px;
    ring-color: #2563eb;
}

.satisfaction-submit {
    width: 100%;
    justify-content: center;
}

/* Styles pour l'affichage de la satisfaction */
.satisfaction-display {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.satisfaction-stars {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    justify-content: center;
}

.satisfaction-stars .star {
    font-size: 1.5rem;
}

.satisfaction-stars .star.filled {
    color: #fbbf24;
}

.satisfaction-stars .star.empty {
    color: #d1d5db;
}

.satisfaction-score {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-left: 0.5rem;
}

.satisfaction-comment-display {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem;
}

.satisfaction-comment-display p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
    font-style: italic;
}

.satisfaction-date {
    text-align: center;
}

.satisfaction-date small {
    color: #9ca3af;
    font-size: 0.75rem;
}
    color: #1f2937;
    margin: 0.5rem 1rem 0.25rem 1rem;
    word-break: break-word;
}

.kb-attachment-size {
    font-size: 0.75rem;
    color: #6b7280;
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

.reply-box {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-top: 2.5rem;
}

/* Modal styles */
.image-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.image-modal-content {
    max-width: 90vw;
    max-height: 90vh;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.image-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: white;
}

.image-modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.image-modal-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.image-modal-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.image-modal-body {
    padding: 0;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}

.image-modal-body img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

.image-modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.image-modal-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.image-modal-actions {
    display: flex;
    gap: 0.5rem;
}

.image-modal-download {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.image-modal-download:hover {
    background: #2563eb;
}

.image-modal-close-btn {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.image-modal-close-btn:hover {
    background: #4b5563;
}

/* Responsive */
@media (max-width: 768px) {
    .image-modal-content {
        max-width: 95vw;
        max-height: 95vh;
    }
}

/* Styles pour les boutons d'actions rapides */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.kb-quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    text-align: left;
}

.kb-quick-action-btn:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    background: white;
}

.kb-quick-action-btn.kb-danger {
    color: #dc2626;
    border-color: #fecaca;
}

.kb-quick-action-btn.kb-danger:hover {
    border-color: #dc2626;
    background: #fef2f2;
    color: #dc2626;
}

.kb-quick-action-btn svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.kb-quick-action-btn span {
    font-weight: 500;
}

/* Styles pour les boutons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid transparent;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-icon {
    padding: 0.5rem;
    background: white;
    border: 1px solid #e5e7eb;
    color: #6b7280;
    border-radius: 0.5rem;
}

.btn-icon:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

.btn-icon:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.btn-secondary {
    background: white;
    color: #374151;
    border-color: #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.btn-outline {
    background: transparent;
    color: #3b82f6;
    border-color: #3b82f6;
}

.btn-outline:hover {
    background: #3b82f6;
    color: white;
}

.reply-toggle {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin-right: 0.5rem;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e7eb;
    transition: 0.4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #2563eb;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.toggle-label {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
    cursor: pointer;
}

.reply-textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem;
    font-size: 0.875rem;
    resize: vertical;
    min-height: 100px;
}

.reply-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.reply-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

/* Styles pour l'assignation */
.assignment-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.assignment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.assignment-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.assignment-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}
</style>
<!-- Modal pour l'affichage des images -->
<div id="imageModal" class="image-modal">
    <div class="image-modal-content">
        <div class="image-modal-header">
            <h3 class="image-modal-title" id="modalTitle">Image</h3>
            <button class="image-modal-close" onclick="closeImageModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="image-modal-body">
            <img id="modalImage" src="" alt="">
        </div>
        <div class="image-modal-footer">
            <div class="image-modal-info" id="modalInfo">
                <span id="modalSize"></span>
            </div>
            <div class="image-modal-actions">
                <a href="" id="modalDownload" class="image-modal-download" download>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Télécharger
                </a>
                <button class="image-modal-close-btn" onclick="closeImageModal()">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les fichiers joints -->
<script>
// Fonction pour ouvrir le modal d'image
function openImageModal(imageUrl, imageName) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalInfo = document.getElementById('modalInfo');
    const modalDownload = document.getElementById('modalDownload');
    
    modalImage.src = imageUrl;
    modalTitle.textContent = imageName;
    modalInfo.textContent = 'Taille: ' + formatFileSize(imageUrl);
    modalDownload.href = imageUrl;
    modalDownload.download = imageName;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Fonction pour fermer le modal d'image
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Fonction pour formater la taille d'un fichier (approximation)
function formatFileSize(url) {
    // C'est une approximation basée sur l'URL
    const size = url.includes('KB') ? 'KB' : 'MB';
    return size;
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Fermer le modal avec la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Bouton "Joindre un fichier"
document.addEventListener('DOMContentLoaded', function() {
    try {
        const attachFileBtn = document.querySelector('[data-testid="attach-file-button"]');
        console.log('Attach button found:', attachFileBtn); // Debug
        
        if (attachFileBtn) {
            attachFileBtn.addEventListener('click', function(e) {
                try {
                    console.log('Attach button clicked'); // Debug
                    e.preventDefault();
                    
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.accept = 'image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar';
                    fileInput.multiple = false;
                    fileInput.style.display = 'none';
                    
                    fileInput.addEventListener('change', function() {
                        try {
                            if (this.files.length > 0) {
                                console.log('Files selected:', this.files.length); // Debug
                                handleFileSelection(this.files);
                            }
                        } catch (error) {
                            console.error('Error in file change handler:', error);
                        }
                    });
                    
                    document.body.appendChild(fileInput);
                    fileInput.click();
                    document.body.removeChild(fileInput);
                } catch (error) {
                    console.error('Error in click handler:', error);
                }
            });
        } else {
            console.error('Attach button not found!');
        }
    } catch (error) {
        console.error('Error in DOMContentLoaded:', error);
    }
});

// Variable pour stocker les fichiers sélectionnés
let selectedFiles = [];

// Fonction pour gérer la sélection de fichiers
function handleFileSelection(files) {
    console.log('Files selected:', files); // Debug
    
    // Ajouter les fichiers à la liste
    for (let file of files) {
        selectedFiles.push(file);
        // Afficher le fichier dans la zone de prévisualisation
        addFilePreview(file);
    }
    
    console.log('Total selected files:', selectedFiles.length); // Debug
}

// Fonction pour ajouter un aperçu du fichier
function addFilePreview(file) {
    const previewContainer = document.querySelector('.reply-files-preview');
    if (!previewContainer) {
        // Créer le conteneur de prévisualisation s'il n'existe pas
        const container = document.createElement('div');
        container.className = 'reply-files-preview';
        container.style.cssText = 'margin-top: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.5rem;';
        document.querySelector('.reply-textarea').after(container);
    }
    
    const filePreview = document.createElement('div');
    filePreview.className = 'file-preview-item';
    filePreview.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f3f4f6; border-radius: 0.5rem; border: 1px solid #e5e7eb;';
    
    filePreview.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <span style="font-size: 0.875rem; color: #374151;">${file.name} (${formatFileSize(file.size)})</span>
        <button type="button" onclick="removeFile('${file.name}')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.25rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    `;
    
    previewContainer.appendChild(filePreview);
}

// Fonction pour supprimer un fichier de la prévisualisation
function removeFile(fileName) {
    selectedFiles = selectedFiles.filter(file => file.name !== fileName);
    
    // Mettre à jour l'affichage
    const previewContainer = document.querySelector('.reply-files-preview');
    if (previewContainer) {
        const items = previewContainer.querySelectorAll('.file-preview-item');
        items.forEach(item => {
            if (item.textContent.includes(fileName)) {
                item.remove();
            }
        });
        
        // Supprimer le conteneur s'il est vide
        if (previewContainer.children.length === 0) {
            previewContainer.remove();
        }
    }
    
    console.log('File removed:', fileName); // Debug
}

// Intercepter l'envoi du formulaire pour inclure les fichiers
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.querySelector('form[action*="tickets.messages.store"]');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            if (selectedFiles.length > 0) {
                e.preventDefault();
                uploadFilesWithMessage(this);
            }
        });
    }

    // Gérer le formulaire de changement de statut
    const statusForm = document.querySelector('form[action*="tickets.status"]');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            
            // Afficher l'état de chargement
            submitBtn.innerHTML = '⏳ Mise à jour...';
            submitBtn.disabled = true;
            
            // Envoyer le formulaire
            fetch(this.action, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(this))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès et recharger la page
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Erreur : ' + (data.error || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                alert('Une erreur est survenue lors de la mise à jour du statut.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            });
        });
    }

    // Gérer le formulaire de satisfaction
    const satisfactionForm = document.getElementById('satisfaction-form');
    if (satisfactionForm) {
        satisfactionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.satisfaction-submit');
            const originalContent = submitBtn.innerHTML;
            
            // Vérifier qu'une note est sélectionnée
            const selectedNote = this.querySelector('input[name="note"]:checked');
            if (!selectedNote) {
                alert('Veuillez sélectionner une note avant d\'envoyer votre feedback.');
                return;
            }
            
            // Afficher l'état de chargement
            submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Envoi en cours...';
            submitBtn.disabled = true;
            
            // Envoyer le formulaire
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(this))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    alert(data.message);
                    // Recharger la page pour afficher la satisfaction enregistrée
                    window.location.reload();
                } else {
                    alert('Erreur : ' + (data.error || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error submitting satisfaction:', error);
                alert('Une erreur est survenue lors de l\'envoi de votre feedback.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            });
        });
    }

    // Gérer le bouton changer la priorité
    const changePriorityBtn = document.querySelector('[data-testid="change-priority-button"]');
    if (changePriorityBtn) {
        changePriorityBtn.addEventListener('click', function() {
            showPriorityModal();
        });
    }

    // Gérer le bouton fusionner ticket
    const mergeTicketBtn = document.querySelector('[data-testid="merge-ticket-button"]');
    if (mergeTicketBtn) {
        mergeTicketBtn.addEventListener('click', function() {
            showMergeModal();
        });
    }

    // Gérer le bouton supprimer ticket
    const deleteTicketBtn = document.querySelector('[data-testid="delete-ticket-button"]');
    if (deleteTicketBtn) {
        deleteTicketBtn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce ticket ? Cette action est irréversible.')) {
                deleteTicket();
            }
        });
    }

    // Gérer le bouton changer le statut
    const changeStatusBtn = document.querySelector('[data-testid="change-status-button"]');
    if (changeStatusBtn) {
        changeStatusBtn.addEventListener('click', function() {
            showStatusModal();
        });
    }
});

// Fonction pour afficher le modal de changement de priorité
function showPriorityModal() {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Changer la priorité</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="priorityForm" method="POST" action="{{ route('tickets.priority', $ticket) }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Nouvelle priorité</label>
                        <select name="priorite_id" class="form-select" required>
                            @foreach($priorites as $priorite)
                                <option value="{{ $priorite->id }}" {{ $ticket->priorite_id == $priorite->id ? 'selected' : '' }}>
                                    {{ $priorite->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Motif du changement</label>
                        <textarea name="motif" class="form-textarea" rows="3" placeholder="Expliquez pourquoi vous changez la priorité..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Changer la priorité</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Gérer la soumission du formulaire
    document.getElementById('priorityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '⏳ Mise à jour...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(this))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Erreur : ' + (data.error || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error updating priority:', error);
            alert('Une erreur est survenue lors de la mise à jour de la priorité.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        });
    });
}

// Fonction pour afficher le modal de fusion
function showMergeModal() {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Fusionner avec un autre ticket</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="mergeForm" method="POST" action="{{ route('tickets.merge', $ticket) }}">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label class="form-label">Référence du ticket à fusionner</label>
                        <input type="text" name="target_ticket_reference" class="form-input" 
                               placeholder="Ex: TICK-2023-001" required>
                        <small class="text-gray-500">Entrez la référence du ticket avec lequel ce ticket sera fusionné</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Motif de la fusion</label>
                        <textarea name="motif" class="form-textarea" rows="3" 
                                  placeholder="Expliquez pourquoi vous fusionnez ces tickets..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <strong>Attention :</strong> Le ticket actuel sera fusionné dans le ticket cible et ne sera plus accessible indépendamment.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Fusionner</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Gérer la soumission du formulaire
    document.getElementById('mergeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '⏳ Fusion en cours...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(this))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = data.redirect || '{{ route("dashboard") }}';
            } else {
                alert('Erreur : ' + (data.error || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error merging ticket:', error);
            alert('Une erreur est survenue lors de la fusion du ticket.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        });
    });
}

// Fonction pour afficher le modal de changement de statut
function showStatusModal() {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Changer le statut</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST" action="{{ route('tickets.status', $ticket) }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Nouveau statut</label>
                        <select name="statut_id" class="form-select" required>
                            <option value="">Choisir un statut...</option>
                            @foreach(\App\Models\TicketStatut::where('nom', '!=', 'Fusionné')->get() as $statut)
                                <option value="{{ $statut->id }}" {{ $ticket->statut_id === $statut->id ? 'selected' : '' }}>
                                    {{ $statut->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Motif du changement</label>
                        <textarea name="motif" class="form-textarea" rows="3" placeholder="Expliquez pourquoi vous changez le statut..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Changer le statut</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Gérer la soumission du formulaire
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '⏳ Mise à jour...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                statut_id: this.statut_id.value,
                motif: this.motif.value,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.closest('.modal').remove();
                location.reload();
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la mise à jour du statut.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        });
    });
    
    // Fermer le modal en cliquant à l'extérieur
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    // Fermer le modal avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.remove();
        }
    });
}

// Fonction pour supprimer un ticket
function deleteTicket() {
    const submitBtn = document.querySelector('[data-testid="delete-ticket-button"]');
    const originalContent = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '⏳ Suppression...';
    submitBtn.disabled = true;
    
    fetch('{{ route('tickets.destroy', $ticket) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route('dashboard') }}';
        } else {
            alert('Erreur : ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error deleting ticket:', error);
        alert('Une erreur est survenue lors de la suppression du ticket.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    });
}

// Fonction pour uploader les fichiers avec le message
function uploadFilesWithMessage(form) {
    const formData = new FormData(form);
    const textarea = form.querySelector('textarea[name="message"]');
    const isInternal = form.querySelector('input[name="interne"]')?.checked || false;
    
    // Ajouter les fichiers au FormData
    selectedFiles.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    
    // Ajouter les autres données
    formData.append('message', textarea.value);
    formData.append('interne', isInternal);
    
    // Afficher un indicateur de chargement
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span>⏳</span> Envoi...';
    submitBtn.disabled = true;
    
    // Envoyer la requête
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Vider la zone de texte et les fichiers
            textarea.value = '';
            selectedFiles = [];
            const previewContainer = document.querySelector('.reply-files-preview');
            if (previewContainer) previewContainer.remove();
            
            // Recharger la page pour voir le nouveau message
            location.reload();
        } else {
            alert('Erreur : ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Une erreur est survenue lors de l\'envoi du message');
    })
    .finally(() => {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    });
}

// Fonction pour uploader un fichier
function uploadFile(file) {
    console.log('Starting upload for:', file.name); // Debug
    
    const formData = new FormData();
    formData.append('file', file);
    
    // Récupérer l'ID du ticket depuis l'URL
    const ticketId = window.location.pathname.split('/')[2];
    console.log('Ticket ID:', ticketId); // Debug
    
    // Afficher un indicateur de chargement
    const loadingBtn = document.querySelector('[data-testid="attach-file-button"]');
    const originalContent = loadingBtn.innerHTML;
    loadingBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>';
    loadingBtn.disabled = true;
    
    const uploadUrl = `/tickets/${ticketId}/attach`;
    console.log('Upload URL:', uploadUrl); // Debug
    
    fetch(uploadUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug
        if (data.success) {
            // Ajouter le fichier à la liste sans recharger la page
            addAttachmentToList(data.attachment);
            alert('Fichier joint avec succès !');
        } else {
            alert('Erreur : ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Upload error:', error); // Debug
        alert('Une erreur est survenue lors du téléchargement du fichier: ' + error.message);
    })
    .finally(() => {
        // Restaurer le bouton
        loadingBtn.innerHTML = originalContent;
        loadingBtn.disabled = false;
    });
}

// Fonction pour ajouter un fichier à la liste
function addAttachmentToList(attachment) {
    // Vérifier si c'est une image ou un document
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp', 'tiff'].includes(
        attachment.extension.toLowerCase()
    );
    
    const attachmentHtml = isImage ? 
        `<div class="kb-attachment-item image-attachment" data-full-src="${attachment.url}" onclick="openImageModal('${attachment.url}', '${attachment.name}')">
            <img src="${attachment.url}" alt="${attachment.name}" class="kb-attachment-thumbnail">
            <span class="kb-attachment-name">${attachment.name}</span>
        </div>` :
        `<a href="${attachment.url}" class="kb-attachment-item file-attachment" download="${attachment.name}">
            <div class="kb-attachment-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <span class="kb-attachment-name">${attachment.name}</span>
            <span class="kb-attachment-size">${attachment.size}</span>
        </a>`;
    
    // Trouver la grille d'attachments et ajouter le nouveau fichier
    const attachmentsGrid = document.querySelector('.kb-attachments-grid');
    if (attachmentsGrid) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = attachmentHtml;
        attachmentsGrid.appendChild(tempDiv.firstElementChild);
    } else {
        console.error('Attachments grid not found');
    }
}
</script>
@endsection
