<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SSOController;
use App\Http\Controllers\Support\AssignmentController;
use App\Http\Controllers\Support\TicketController;
use App\Http\Controllers\Support\DashboardController;
use App\Http\Controllers\Support\ReportsController;
use App\Http\Controllers\Support\KnowledgeBaseController;
use App\Http\Controllers\Support\TeamController;
use App\Http\Controllers\Support\TicketMessageController;
use App\Http\Controllers\Support\NotificationsController;
use App\Http\Controllers\Support\AdminController;
use App\Models\Plateforme;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification pour les utilisateurs internes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');

// API route pour les modules par plateforme
Route::get('/api/plateformes/{plateforme}/modules', function (Plateforme $plateforme) {
    return $plateforme->modules()->where('etat', true)->get(['id', 'nom']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    Route::get('/dashboard', [App\Http\Controllers\Support\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-tickets', [App\Http\Controllers\Support\MyTicketsController::class, 'index'])->name('my-tickets.index');
    Route::get('/notifications', [App\Http\Controllers\Support\NotificationsController::class, 'page'])->name('notifications.page');
    
    // Routes pour l'assignation (techniciens et admins)
    Route::middleware(['role:Technicien,Administrateur'])->prefix('assignment')->name('assignment.')->group(function () {
        Route::get('/dashboard', [AssignmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/unassigned', [AssignmentController::class, 'unassigned'])->name('unassigned');
        Route::post('/assign/{ticket}', [AssignmentController::class, 'assign'])->name('assign');
        Route::post('/reassign/{ticket}', [AssignmentController::class, 'reassign'])->name('reassign');
        Route::post('/auto-assign', [AssignmentController::class, 'autoAssign'])->name('auto-assign');
        Route::get('/stats', [AssignmentController::class, 'stats'])->name('stats');
    });
    
    // Routes pour l'administration (réservé aux admins)
    Route::middleware(['role:Administrateur'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        Route::get('/plateformes', [AdminController::class, 'plateformes'])->name('plateformes');
        Route::get('/plateformes/create', [AdminController::class, 'createPlateforme'])->name('plateformes.create');
        Route::post('/plateformes', [AdminController::class, 'storePlateforme'])->name('plateformes.store');
        Route::get('/plateformes/{plateforme}/edit', [AdminController::class, 'editPlateforme'])->name('plateformes.edit');
        Route::put('/plateformes/{plateforme}', [AdminController::class, 'updatePlateforme'])->name('plateformes.update');
        Route::delete('/plateformes/{plateforme}', [AdminController::class, 'deletePlateforme'])->name('plateformes.delete');
        
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
    
    // Routes pour les tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])->name('tickets.messages.store');
    Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('tickets.resolve');
    Route::post('/tickets/{ticket}/satisfaction', [TicketController::class, 'submitSatisfaction'])->name('tickets.satisfaction');
    Route::get('/tickets/{ticket}/history', [\App\Http\Controllers\Support\TicketHistoryController::class, 'show'])->name('tickets.history');
    Route::get('/api/tickets/{ticket}/history', [\App\Http\Controllers\Support\TicketHistoryController::class, 'api'])->name('tickets.history.api');
    Route::match(['put', 'patch'], '/tickets/{ticket}/priority', [TicketController::class, 'updatePriority'])->name('tickets.priority');
    Route::match(['put', 'patch'], '/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/merge', [TicketController::class, 'mergeTicket'])->name('tickets.merge');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    
    // Routes pour les fichiers joints
    Route::post('/tickets/{ticket}/attach', [TicketController::class, 'attachFile'])->name('tickets.attach.file');
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'removeAttachment'])->name('tickets.attachments.remove');
    
    // Routes pour les fichiers de la base de connaissances
    Route::post('/knowledge-base/{article}/attach', [KnowledgeBaseController::class, 'attachFile'])->name('knowledge-base.attach.file');
    Route::delete('/knowledge-base/{article}/attachments/{attachment}', [KnowledgeBaseController::class, 'removeAttachment'])->name('knowledge-base.attachments.remove');
    Route::post('/knowledge-base/{article}/attachments/{attachment}/primary', [KnowledgeBaseController::class, 'setPrimary'])->name('knowledge-base.attachments.primary');
    
    // Routes pour les notifications (API)
    Route::get('/api/notifications', [NotificationsController::class, 'index']);
    Route::get('/api/notifications/count', [NotificationsController::class, 'count']);
    Route::get('/api/notifications/recent', [NotificationsController::class, 'recent']);
    Route::get('/api/tickets/{ticket}', function(Ticket $ticket) {
        return response()->json([
            'id' => $ticket->id,
            'reference' => $ticket->reference,
            'titre' => $ticket->titre,
        ]);
    });
    
    Route::get('/api/tickets/search', function(Request $request) {
        $reference = $request->get('reference');
        if (!$reference) {
            return response()->json(['error' => 'Référence requise'], 400);
        }
        
        $ticket = Ticket::where('reference', $reference)->first();
        
        if ($ticket) {
            return response()->json(['ticket' => [
                'id' => $ticket->id,
                'reference' => $ticket->reference,
                'titre' => $ticket->titre,
            ]]);
        }
        
        return response()->json(['error' => 'Ticket non trouvé'], 404);
    });
    Route::post('/api/notifications', [NotificationsController::class, 'store']);
    Route::patch('/api/notifications/{notification}/read', [NotificationsController::class, 'markAsRead']);
    Route::patch('/api/notifications/read-all', [NotificationsController::class, 'markAllAsRead']);
    Route::delete('/api/notifications/{notification}', [NotificationsController::class, 'destroy']);
    Route::delete('/api/notifications/clear', [NotificationsController::class, 'clear']);
    
    // Route pour les statistiques de superviseur
    Route::get('/supervisor/stats', [App\Http\Controllers\Support\SupervisorDashboardController::class, 'index'])
        ->name('supervisor.stats')
        ->middleware(['auth', 'permission:access-supervisor-stats']); // Middleware de permission personnalisé
    
    // Routes pour les rapports (superviseurs)
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index')->middleware(['auth', 'role:Superviseur,Administrateur']);
    Route::post('/reports/export/tickets', [ReportsController::class, 'exportTickets'])->name('reports.export.tickets')->middleware(['auth', 'role:Superviseur,Administrateur']);
    Route::post('/reports/export/performance', [ReportsController::class, 'exportPerformance'])->name('reports.export.performance')->middleware(['auth', 'role:Superviseur,Administrateur']);
    Route::post('/reports/export/techniciens', [ReportsController::class, 'exportTechniciens'])->name('reports.export.techniciens')->middleware(['auth', 'role:Superviseur,Administrateur']);
    Route::get('/reports/api-data', [ReportsController::class, 'apiData'])->name('reports.api.data')->middleware(['auth', 'role:Superviseur,Administrateur']);
    
    // Routes pour la base de connaissances
    Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::get('/knowledge-base/create', [KnowledgeBaseController::class, 'create'])->name('knowledge-base.create');
    Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
    Route::get('/knowledge-base/dashboard', [KnowledgeBaseController::class, 'dashboard'])->name('knowledge-base.dashboard');
    Route::get('/knowledge-base/search', [KnowledgeBaseController::class, 'search'])->name('knowledge-base.search');
    Route::get('/knowledge-base/{article}/edit', [KnowledgeBaseController::class, 'edit'])->name('knowledge-base.edit');
    Route::put('/knowledge-base/{article}', [KnowledgeBaseController::class, 'update'])->name('knowledge-base.update');
    Route::delete('/knowledge-base/{article}', [KnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');
    Route::get('/knowledge-base/{article}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');
    Route::post('/knowledge-base/{article}/helpful', [KnowledgeBaseController::class, 'markHelpful'])->name('knowledge-base.helpful');
    Route::post('/knowledge-base/{article}/not-helpful', [KnowledgeBaseController::class, 'markNotHelpful'])->name('knowledge-base.not-helpful');
    
    // Routes pour la gestion des équipes (superviseurs/administrateurs)
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/dashboard', [TeamController::class, 'dashboard'])->name('teams.dashboard');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    
    // API pour la gestion des membres
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::post('/teams/{team}/members/{user}/promote', [TeamController::class, 'promoteMember'])->name('teams.members.promote');
});
