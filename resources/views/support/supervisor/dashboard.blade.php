@extends('layouts.app')

@section('content')
<!-- Header du superviseur -->
<header class="header" data-testid="supervisor-header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Statistiques de Supervision</h1>
            <p class="page-subtitle">Vue d'ensemble des performances du support</p>
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Tickets</p>
            <p class="text-3xl font-black text-gray-900 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest">En cours</p>
            <p class="text-3xl font-black text-blue-600 mt-2">{{ $stats['ouverts'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500">
            <p class="text-xs font-bold text-green-500 uppercase tracking-widest">Résolus</p>
            <p class="text-3xl font-black text-green-600 mt-2">{{ $stats['resolus'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest">Délai Moyen (SLA)</p>
            <p class="text-3xl font-black text-amber-600 mt-2">{{ round($tempsMoyenHeures, 1) }}h</p>
        </div>
    </div>

    {{-- Répartition par Plateforme --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="font-bold text-gray-700">Volume par Plateforme</h2>
        </div>
        <div class="p-6">
            @foreach($parPlateforme as $plat)
            <div class="mb-6 last:mb-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-700">{{ $plat->nom }}</span>
                    <span class="text-sm text-gray-500">{{ $plat->tickets_count }} tickets ({{ round($plat->pourcentage, 1) }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $plat->pourcentage }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
