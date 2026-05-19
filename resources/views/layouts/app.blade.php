<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - CJES Support')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <!-- Navigation Sidebar -->
    <aside class="sidebar" id="sidebar" data-testid="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="{{ asset('favicon.png') }}" alt="CJES Support" class="logo-icon">
                <span class="logo-text">CJES Support</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-testid="nav-dashboard">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                <span>Dashboard</span>
            </a>
            @if(in_array(auth()->user()->role->titre, ['Technicien', 'Superviseur', 'Administrateur']))
            <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.index') ? 'active' : '' }}" data-testid="nav-tickets">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span>Tickets</span>
            </a>
            @endif
            @if(auth()->user()->role->titre === 'Demandeur')
            <a href="{{ route('my-tickets.index') }}" class="nav-item {{ request()->routeIs('my-tickets.*') ? 'active' : '' }}" data-testid="nav-my-tickets">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Mes Tickets</span>
            </a>
            @endif
            @if(auth()->user()->role->titre === 'Demandeur')
            <a href="{{ route('tickets.create') }}" class="nav-item {{ request()->routeIs('tickets.create') ? 'active' : '' }}" data-testid="nav-create-ticket">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Nouveau Ticket</span>
            </a>
            @endif
            <a href="{{ route('knowledge-base.index') }}" class="nav-item {{ request()->routeIs('knowledge-base.*') ? 'active' : '' }}" data-testid="nav-knowledge-base">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    <path d="M12 3v18"/>
                </svg>
                <span>Base de Connaissances</span>
            </a>
            @if (in_array(auth()->user()->role->titre, ['Superviseur', 'Administrateur']))
            <a href="{{ route('teams.index') }}" class="nav-item {{ request()->routeIs('teams.*') ? 'active' : '' }}" data-testid="nav-teams">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Équipes</span>
            </a>
            @endif
            
            @if (in_array(auth()->user()->role->titre, ['Superviseur', 'Administrateur']))
            <a href="{{ route('supervisor.stats') }}" class="nav-item {{ request()->routeIs('supervisor.stats') ? 'active' : '' }}" data-testid="nav-stats">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/>
                    <path d="M3 9h18"/>
                    <path d="M3 15h18"/>
                    <path d="M9 3v18"/>
                    <path d="M15 3v18"/>
                </svg>
                <span>Statistiques</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-testid="nav-reports">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span>Rapports</span>
            </a>
            @endif
            
            @if(in_array(Auth::user()->role->titre, ['Technicien', 'Administrateur']))
            <a href="{{ route('assignment.dashboard') }}" class="nav-item {{ request()->routeIs('assignment.*') ? 'active' : '' }}" data-testid="nav-assignment">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Assignations</span>
            </a>
            @endif
            
            @if(Auth::user()->role->titre === 'Administrateur')
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" data-testid="nav-admin">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3m16.24-6.76l-4.24 4.24M9 9l-4.24 4.24"/>
                </svg>
                <span>Administration</span>
            </a>
            @endif
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;" data-testid="user-avatar">
                    {{ strtoupper(substr(Auth::user()->nom, 0, 1)) }}{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</div>
                    <div class="user-role">{{ Auth::user()->role->titre }}</div>
                </div>
            </div>
            
            <!-- Bouton de déconnexion -->
            <form action="{{ route('logout') }}" method="POST" class="logout-form" data-testid="logout-form">
                @csrf
                <button type="submit" class="logout-btn" data-testid="logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16,17 21,12 16,7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    
    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (mobileMenuToggle && sidebar) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    
                    // Close menu when clicking outside
                    if (sidebar.classList.contains('open')) {
                        setTimeout(function() {
                            document.addEventListener('click', closeMenuOutside);
                        }, 100);
                    } else {
                        document.removeEventListener('click', closeMenuOutside);
                    }
                });
                
                function closeMenuOutside(e) {
                    if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                        document.removeEventListener('click', closeMenuOutside);
                    }
                }
                
                // Close menu on window resize if desktop
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('open');
                    }
                });
            }
            
            // Add smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
        
        // Améliorations UI/UX générales
        // 1. Gestion du thème (clair/sombre)
        const initTheme = () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Synchroniser le toggle de thème
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.checked = savedTheme === 'dark';
                themeToggle.addEventListener('change', function() {
                    const newTheme = this.checked ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                });
            }
        };
        
        // 2. Animations d'apparition au scroll
        const initScrollAnimations = () => {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);
            
            // Observer les éléments principaux
            document.querySelectorAll('.stat-card, .chart-card, .ticket-card, .team-card').forEach(el => {
                observer.observe(el);
            });
        };
        
        // 3. Amélioration de l'accessibilité
        const initAccessibility = () => {
            // Gestion du focus clavier
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-nav');
                }
            });
            
            document.addEventListener('mousedown', function() {
                document.body.classList.remove('keyboard-nav');
            });
            
            // Améliorer les contrastes
            const enhanceContrast = () => {
                const cards = document.querySelectorAll('.card, .btn, .modal-content');
                cards.forEach(card => {
                    card.style.outline = '2px solid transparent';
                    card.addEventListener('focus', function() {
                        this.style.outlineColor = '#3b82f6';
                        this.style.outlineOffset = '2px';
                    });
                    card.addEventListener('blur', function() {
                        this.style.outlineColor = 'transparent';
                    });
                });
            };
            
            enhanceContrast();
        };
        
        // 4. Indicateurs de chargement globaux
        const initLoadingIndicators = () => {
            // Créer un indicateur de chargement global
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'global-loading';
            loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
            loadingOverlay.innerHTML = `
                <div class="bg-white rounded-lg p-4 flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Chargement...</span>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
            
            // Fonctions pour afficher/masquer le chargement
            window.showLoading = () => {
                document.getElementById('global-loading').classList.remove('hidden');
            };
            
            window.hideLoading = () => {
                document.getElementById('global-loading').classList.add('hidden');
            };
        };
        
        // 5. Notifications toast améliorées
        const initToasts = () => {
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
            
            window.showToast = (message, type = 'info') => {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type} transform translate-x-full transition-transform duration-300 ease-out`;
                
                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };
                
                const colors = {
                    success: 'bg-green-500 text-white',
                    error: 'bg-red-500 text-white',
                    warning: 'bg-amber-500 text-white',
                    info: 'bg-blue-500 text-white'
                };
                
                toast.innerHTML = `
                    <div class="flex items-center space-x-3 ${colors[type]} px-4 py-3 rounded-lg shadow-lg">
                        <span class="text-xl">${icons[type]}</span>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                `;
                
                toastContainer.appendChild(toast);
                
                // Animation d'entrée
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);
                
                // Auto-suppression
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            };
        };
        
        // 6. Amélioration des formulaires
        const initFormEnhancements = () => {
            // Validation en temps réel
            document.querySelectorAll('form').forEach(form => {
                const inputs = form.querySelectorAll('input, textarea, select');
                
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        validateField(this);
                    });
                    
                    input.addEventListener('input', function() {
                        // Effacer les erreurs lors de la saisie
                        this.classList.remove('border-red-500');
                        const errorMsg = this.parentElement.querySelector('.field-error');
                        if (errorMsg) errorMsg.remove();
                    });
                });
            });
            
            function validateField(field) {
                const isValid = field.checkValidity();
                const errorMsg = field.parentElement.querySelector('.field-error');
                
                if (!isValid && field.value.trim() !== '') {
                    field.classList.add('border-red-500');
                    if (!errorMsg) {
                        const error = document.createElement('div');
                        error.className = 'field-error text-red-500 text-sm mt-1';
                        error.textContent = field.validationMessage || 'Ce champ est requis';
                        field.parentElement.appendChild(error);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    if (errorMsg) errorMsg.remove();
                }
            }
        };
        
        // Initialiser toutes les améliorations
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            initScrollAnimations();
            initAccessibility();
            initLoadingIndicators();
            initToasts();
            initFormEnhancements();
            
            // Ajouter des animations CSS personnalisées
            const style = document.createElement('style');
            style.textContent = `
                @keyframes animateIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .animate-in {
                    animation: animateIn 0.6s ease-out forwards;
                }
                
                .keyboard-nav *:focus {
                    outline: 2px solid #3b82f6 !important;
                    outline-offset: 2px !important;
                }
                
                .toast {
                    min-width: 300px;
                    max-width: 500px;
                }
                
                [data-theme="dark"] {
                    background-color: #1f2937;
                    color: #f9fafb;
                }
                
                [data-theme="dark"] .bg-white {
                    background-color: #374151 !important;
                    color: #f9fafb !important;
                }
                
                [data-theme="dark"] .text-gray-700 {
                    color: #d1d5db !important;
                }
                
                [data-theme="dark"] .border-gray-100 {
                    border-color: #4b5563 !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
