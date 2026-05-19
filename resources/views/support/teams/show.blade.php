@extends('layouts.app')

@section('title', $team->name . ' - CJES Support')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">{{ $team->name }}</h1>
            <p class="page-subtitle">
                <span class="team-badge" style="background-color: {{ $team->color }}20; color: {{ $team->color }};">
                    {{ $team->member_count }} membre{{ $team->member_count > 1 ? 's' : '' }}
                </span>
                <span class="meta-separator">·</span>
                <span class="team-supervisor">Supervisé par {{ $team->supervisor->nom }} {{ $team->supervisor->prenom }}</span>
            </p>
        </div>
        <div class="header-right">
            <a href="{{ route('teams.edit', $team) }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Modifier
            </a>
            <a href="{{ route('teams.index') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</header>

<!-- Team Details -->
<div class="team-details-container">
    <div class="team-main-content">
        <!-- Team Information -->
        <div class="team-info-card">
            <h2 class="section-title">Informations de l'équipe</h2>
            
            <div class="team-description">
                <h3>Description</h3>
                <p>{{ $team->description ?: 'Aucune description disponible pour cette équipe.' }}</p>
            </div>
            
            <div class="team-meta">
                <div class="meta-item">
                    <strong>Créée le :</strong> {{ $team->created_at->format('d/m/Y à H:i') }}
                </div>
                <div class="meta-item">
                    <strong>Dernière mise à jour :</strong> {{ $team->updated_at->format('d/m/Y à H:i') }}
                </div>
                <div class="meta-item">
                    <strong>Couleur :</strong> 
                    <span class="color-badge" style="background-color: {{ $team->color }};">
                        {{ $team->color }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Team Members -->
        <div class="team-members-card">
            <div class="section-header">
                <h2 class="section-title">Membres de l'équipe</h2>
                <span class="member-count">{{ $team->activeMembers->count() }} membre{{ $team->activeMembers->count() > 1 ? 's' : '' }}</span>
            </div>
            
            @if($team->activeMembers->count() > 0)
            <div class="members-list">
                @foreach($team->activeMembers as $member)
                <div class="member-item" style="border-left: 4px solid {{ $member->role_color }};">
                    <div class="member-avatar">
                        <div class="user-avatar-medium" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px;">
                            {{ strtoupper(substr($member->user->nom, 0, 1)) }}{{ strtoupper(substr($member->user->prenom, 0, 1)) }}
                        </div>
                    </div>
                    <div class="member-info">
                        <h4 class="member-name">{{ $member->user->nom }} {{ $member->user->prenom }}</h4>
                        <div class="member-details">
                            <span class="member-role-badge" style="background-color: {{ $member->role_color }}20; color: {{ $member->role_color }};">
                                {{ $member->role_label }}
                            </span>
                            <span class="member-date">Depuis le {{ $member->joined_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="member-actions">
                        @if(Auth::user()->role->titre === 'Administrateur' || (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id === Auth::id()))
                        <div class="action-dropdown">
                            <button class="action-btn" onclick="toggleMemberActions({{ $member->id }})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="1"/>
                                    <circle cx="12" cy="5" r="1"/>
                                    <circle cx="12" cy="19" r="1"/>
                                </svg>
                            </button>
                            <div id="member-actions-{{ $member->id }}" class="action-menu" style="display: none;">
                                @if(!$member->isLeader())
                                <button onclick="promoteMember({{ $team->id }}, {{ $member->user_id }})" class="action-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    Promouvoir leader
                                </button>
                                @endif
                                <button onclick="removeMember({{ $team->id }}, {{ $member->user_id }})" class="action-item action-danger">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3,6 5,6 21,6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                    Retirer de l'équipe
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-members">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h4>Aucun membre dans cette équipe</h4>
                <p>Ajoutez des techniciens pour compléter cette équipe.</p>
                @if(Auth::user()->role->titre === 'Administrateur' || (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id === Auth::id()))
                <a href="{{ route('teams.edit', $team) }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    Ajouter des membres
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <aside class="team-sidebar">
        <!-- Quick Actions -->
        @if(Auth::user()->role->titre === 'Administrateur' || (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id === Auth::id()))
        <div class="sidebar-section">
            <h3 class="sidebar-title">Actions rapides</h3>
            <div class="quick-actions">
                <a href="{{ route('teams.edit', $team) }}" class="action-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Modifier l'équipe
                </a>
                <button onclick="showAddMemberModal()" class="action-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    Ajouter un membre
                </button>
            </div>
        </div>
        @endif
        
        <!-- Team Statistics -->
        <div class="sidebar-section">
            <h3 class="sidebar-title">Statistiques</h3>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Membres actifs</span>
                    <span class="stat-label">{{ $team->activeMembers->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Leader d'équipe</span>
                    <span class="stat-label">{{ $team->leader ? $team->leader->nom . ' ' . $team->leader->prenom : 'Non défini' }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Ancienneté moyenne</span>
                    <span class="stat-label">{{ $team->activeMembers->count() > 0 ? round($team->activeMembers->avg(function($m) { return now()->diffInDays($m->joined_at); }), 0) . ' jours' : 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="sidebar-section">
            <h3 class="sidebar-title">Navigation</h3>
            <div class="nav-links">
                <a href="{{ route('teams.index') }}" class="nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Retour aux équipes
                </a>
                <a href="{{ route('teams.dashboard') }}" class="nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18"/>
                        <path d="M3 9h18"/>
                        <path d="M3 15h18"/>
                        <path d="M9 3v18"/>
                        <path d="M15 3v18"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </aside>
</div>

<!-- Add Member Modal -->
<div id="addMemberModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ajouter un membre à l'équipe</h3>
            <button onclick="hideAddMemberModal()" class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <form id="addMemberForm">
                @csrf
                <div class="form-group">
                    <label for="new_member_id">Technicien</label>
                    <select id="new_member_id" name="user_id" class="form-select" required>
                        <option value="">Sélectionnez un technicien</option>
                        <!-- Options would be populated dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_member_role">Rôle</label>
                    <select id="new_member_role" name="role" class="form-select" required>
                        <option value="member">Membre</option>
                        <option value="leader">Leader d'équipe</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="hideAddMemberModal()" class="btn btn-outline">Annuler</button>
            <button onclick="addMember({{ $team->id }})" class="btn btn-primary">Ajouter</button>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.team-details-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

.team-main-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.team-info-card,
.team-members-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--gray-900);
}

.team-description h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-800);
}

.team-description p {
    color: var(--gray-600);
    line-height: 1.6;
}

.team-meta {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.meta-item {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem;
}

.color-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.member-count {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    background: var(--gray-50);
    transition: all 0.2s;
}

.member-item:hover {
    background: var(--gray-100);
}

.member-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.member-info {
    flex: 1;
}

.member-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.member-details {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.member-role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.member-date {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.member-actions {
    position: relative;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--gray-200);
    border-radius: 6px;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.action-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

.action-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: var(--shadow);
    z-index: 10;
    min-width: 180px;
    margin-top: 0.5rem;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem 1rem;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
    font-size: 0.875rem;
    color: var(--gray-700);
    transition: all 0.2s;
}

.action-item:hover {
    background: var(--gray-50);
    color: var(--gray-900);
}

.action-danger {
    color: var(--danger);
}

.action-danger:hover {
    background: rgba(239, 68, 68, 0.1);
}

.empty-members {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.empty-members h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.empty-members p {
    color: var(--gray-600);
    margin: 0 0 1.5rem 0;
}

.team-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.sidebar-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    margin-bottom: 1.5rem;
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.quick-actions,
.nav-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.action-link,
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    color: var(--gray-700);
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s;
    font-size: 0.875rem;
}

.action-link:hover,
.nav-link:hover {
    background: var(--gray-50);
    color: var(--primary);
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

.stat-label {
    color: var(--gray-600);
}

.stat-value {
    font-weight: 500;
    color: var(--gray-900);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-400);
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: var(--gray-100);
    color: var(--gray-600);
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
    font-size: 0.875rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
}

.modal-footer {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.meta-separator {
    margin: 0 0.5rem;
    color: var(--gray-400);
}

.team-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.team-supervisor {
    font-size: 0.875rem;
    color: var(--gray-600);
}

@media (max-width: 1024px) {
    .team-details-container {
        grid-template-columns: 1fr;
    }
    
    .team-sidebar {
        position: static;
        order: 2;
    }
    
    .team-main-content {
        order: 1;
    }
}

@media (max-width: 768px) {
    .team-details-container {
        padding: 1rem;
    }
    
    .team-info-card,
    .team-members-card {
        padding: 1.5rem;
    }
    
    .member-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .member-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<!-- Scripts -->
<script>
function toggleMemberActions(memberId) {
    const menu = document.getElementById(`member-actions-${memberId}`);
    const allMenus = document.querySelectorAll('.action-menu');
    
    allMenus.forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
        }
    });
    
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function showAddMemberModal() {
    document.getElementById('addMemberModal').style.display = 'flex';
}

function hideAddMemberModal() {
    document.getElementById('addMemberModal').style.display = 'none';
}

function addMember(teamId) {
    const form = document.getElementById('addMemberForm');
    const formData = new FormData(form);
    
    fetch(`/teams/${teamId}/members`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideAddMemberModal();
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function promoteMember(teamId, userId) {
    if (confirm('Êtes-vous sûr de vouloir promouvoir ce membre en leader d\'équipe ?')) {
        fetch(`/teams/${teamId}/members/${userId}/promote`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function removeMember(teamId, userId) {
    if (confirm('Êtes-vous sûr de vouloir retirer ce membre de l\'équipe ?')) {
        fetch(`/teams/${teamId}/members/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.member-actions')) {
        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
</script>
@endsection
