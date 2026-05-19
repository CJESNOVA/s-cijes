@extends('layouts.app')

@section('content')
<!-- Header du superviseur -->
<header class="header" data-testid="supervisor-header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Tableau de Bord Superviseur</h1>
            <p class="page-subtitle">Métriques de performance et analyse des tendances</p>
        </div>
        <div class="header-right">
            <!-- Bouton de déconnexion dans le header -->
            <form action="{{ route('logout') }}" method="POST" class="inline" data-testid="supervisor-logout-form">
                @csrf
                <button type="submit" class="icon-button logout-header-btn" data-testid="supervisor-logout-btn" title="Déconnexion">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16,17 21,12 16,7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<div class="container mx-auto py-8 px-4">
    {{-- Grille de KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Tickets</p>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002 2V7a2 2 0 00-2-2H9z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Depuis le début</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-blue-500 uppercase tracking-widest">En Cours</p>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-blue-600">{{ number_format($stats['ouverts']) }}</p>
            <p class="text-xs text-blue-500 mt-1">Actifs</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-green-500 uppercase tracking-widest">Résolus</p>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-green-600">{{ number_format($stats['resolus']) }}</p>
            <p class="text-xs text-green-500 mt-1">Complétés</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-amber-500 uppercase tracking-widest">SLA Moyen</p>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-amber-600">{{ round($tempsMoyenHeures, 1) }}h</p>
            <p class="text-xs text-amber-500 mt-1">Temps de résolution</p>
        </div>
    </div>

    {{-- Section Répartition par Plateforme --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-700 flex items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2v-6a2 2 0 00-2-2H9z"/>
                        <path d="M23 9v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </svg>
                    Volume par Plateforme
                </h2>
                <span class="text-sm text-gray-500">{{ $parPlateforme->count() }} plateformes</span>
            </div>
        </div>
        <div class="p-6">
            @if($parPlateforme->count() > 0)
                @foreach($parPlateforme as $plat)
                <div class="mb-6 last:mb-0">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded-full mr-3"></div>
                            <span class="font-semibold text-gray-700">{{ $plat->nom }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">{{ number_format($plat->tickets_count) }}</span>
                            <span class="text-sm text-gray-500 ml-2">({{ round($plat->pourcentage, 1) }}%)</span>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="w-full bg-gray-100 rounded-full h-4">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-700 ease-out" 
                                 style="width: {{ $plat->pourcentage }}%"
                                 data-percentage="{{ round($plat->pourcentage, 1) }}"></div>
                        </div>
                        @if($plat->pourcentage > 50)
                            <div class="absolute -top-1 right-0 bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                TOP
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto mb-4">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <p class="text-lg font-medium">Aucune plateforme trouvée</p>
                    <p class="text-sm mt-2">Les données de plateforme s'afficheront ici une fois configurées.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Répartition des tickets par statut -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M11 3.055A9.001 9.001 0 010-18 0h2a5 5 0 015 5v14a5 5 0 01-5-5H4a2 2 0 00-2 2v14a2 2 0 002 2h6l3-3h2v-6a2 2 0 00-2-2H7z"/>
                </svg>
                Répartition des Tickets
            </h3>
            <div class="relative h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        
        <!-- Graphique 2: Volume par plateforme -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M19 11H5m14 0a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 00-2-2v-4H3a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002 2z"/>
                </svg>
                Volume par Plateforme
            </h3>
            <div class="relative h-64">
                <canvas id="platformChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Section Tendances et Performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique 3: Tendance des tickets (7 derniers jours) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M13 7h8m0 0v8m0-8l-8 8-4-4m6 4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tendance des Tickets (7 jours)
            </h3>
            <div class="relative h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        
        <!-- Graphique 4: Performance SLA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
                Performance SLA (7 jours)
            </h3>
            <div class="relative h-64">
                <canvas id="slaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Section Alertes et Métriques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M13 7h8m0 0v8m0-8l-8 8-4-4m6 4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Performance Récente
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Tickets aujourd'hui</span>
                    <span class="text-sm font-semibold text-gray-900 animate-number" data-final="{{ App\Models\Ticket::whereDate('created_at', today())->count() }}">0</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Résolution moyenne</span>
                    <span class="text-sm font-semibold text-green-600">{{ round($tempsMoyenHeures, 1) }}h</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Taux de résolution</span>
                    <span class="text-sm font-semibold text-blue-600">
                        {{ $stats['total'] > 0 ? round(($stats['resolus'] / $stats['total']) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Alertes Actives
            </h3>
            <div class="space-y-3">
                <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-3 animate-pulse"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-800">Tickets critiques sans réponse</p>
                        <p class="text-xs text-red-600 mt-1" data-critical-count>
                            {{ App\Models\Ticket::where('priorite_id', 4)->where('statut_id', 1)->where('created_at', '<=', now()->subHours(2))->count() }} 
                            ticket(s) > 2h
                        </p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <div class="w-2 h-2 bg-amber-500 rounded-full mr-3"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-amber-800">Tickets en attente d'assignation</p>
                        <p class="text-xs text-amber-600 mt-1" data-unassigned-count>
                            {{ App\Models\Ticket::whereNull('technicien_id')->where('statut_id', 1)->count() }} 
                            ticket(s) non assigné(s)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclusion des scripts Chart.js --}}
@include('support.supervisor.chart-scripts')

{{-- Scripts additionnels pour animations et interactions --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des nombres
    const animateNumbers = () => {
        document.querySelectorAll('.animate-number').forEach(element => {
            const final = parseInt(element.dataset.final);
            const duration = 2000;
            const start = 0;
            const increment = final / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= final) {
                    current = final;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
        });
    };
    
    // Rafraîchissement automatique des alertes
    const refreshAlerts = () => {
        // Tickets critiques sans réponse
        const criticalCount = {{ App\Models\Ticket::where('priorite_id', 4)->where('statut_id', 1)->where('created_at', '<=', now()->subHours(2))->count() }};
        const criticalElement = document.querySelector('[data-critical-count]');
        if (criticalElement) {
            criticalElement.textContent = criticalCount;
            criticalElement.className = criticalCount > 0 ? 'animate-pulse text-red-600' : 'text-gray-500';
        }
        
        // Tickets non assignés
        const unassignedCount = {{ App\Models\Ticket::whereNull('technicien_id')->where('statut_id', 1)->count() }};
        const unassignedElement = document.querySelector('[data-unassigned-count]');
        if (unassignedElement) {
            unassignedElement.textContent = unassignedCount;
            unassignedElement.className = unassignedCount > 0 ? 'animate-pulse text-amber-600' : 'text-gray-500';
        }
    };
    
    // Initialisation
    animateNumbers();
    
    // Rafraîchissement toutes les 30 secondes
    setInterval(refreshAlerts, 30000);
    
    // Effet de survol sur les barres de progression
    document.querySelectorAll('[data-percentage]').forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            this.style.transform = 'scaleY(1.05)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        bar.addEventListener('mouseleave', function() {
            this.style.transform = 'scaleY(1)';
        });
    });
});
</script>
</div>
@endsection
