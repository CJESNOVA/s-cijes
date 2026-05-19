<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plateforme;
use App\Models\Ticket;
use App\Models\Role;
use App\Models\TicketStatut;
use App\Models\TicketPriorite;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Dashboard administrateur avec statistiques avancées
     */
    public function dashboard()
    {
        // Statistiques globales
        $stats = [
            'total_users' => User::count(),
            'total_tickets' => Ticket::count(),
            'total_notifications' => Notification::count(),
            'total_plateformes' => Plateforme::count(),
            
            'tickets_ouverts' => Ticket::where('statut_id', 1)->count(),
            'tickets_en_cours' => Ticket::where('statut_id', 3)->count(),
            'tickets_resolus' => Ticket::where('statut_id', 4)->count(),
            
            'urgent_tickets' => Ticket::join('ticket_priorites', 'tickets.priorite_id', '=', 'ticket_priorites.id')
                ->where('ticket_priorites.niveau', '>=', 3)
                ->count(),
        ];

        // Tickets par mois (6 derniers mois)
        $ticketsParMois = Ticket::selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'asc')
            ->orderBy('mois', 'asc')
            ->get();

        // Tickets par statut
        $ticketsParStatut = Ticket::join('ticket_statuts', 'tickets.statut_id', '=', 'ticket_statuts.id')
            ->selectRaw('ticket_statuts.nom as statut, COUNT(*) as count')
            ->groupBy('ticket_statuts.id', 'ticket_statuts.nom')
            ->get();

        // Tickets par priorité
        $ticketsParPriorite = Ticket::join('ticket_priorites', 'tickets.priorite_id', '=', 'ticket_priorites.id')
            ->selectRaw('ticket_priorites.nom as priorite, COUNT(*) as count')
            ->groupBy('ticket_priorites.id', 'ticket_priorites.nom')
            ->get();

        // Utilisateurs par rôle
        $usersByRole = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->selectRaw('roles.titre as role, COUNT(*) as count')
            ->groupBy('roles.id', 'roles.titre')
            ->get();

        // Tickets récents
        $recentTickets = Ticket::with(['user', 'technicien', 'statut', 'priorite'])
            ->latest()
            ->take(10)
            ->get();

        // Utilisateurs récents
        $recentUsers = User::with(['role', 'plateforme'])
            ->latest()
            ->take(5)
            ->get();

        return view('support.admin.dashboard', compact(
            'stats',
            'ticketsParMois',
            'ticketsParStatut',
            'ticketsParPriorite',
            'usersByRole',
            'recentTickets',
            'recentUsers'
        ));
    }

    /**
     * Liste des utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::with(['role', 'plateforme']);
        
        // Filtres
        if ($request->role) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('titre', $request->role);
            });
        }
        
        if ($request->plateforme) {
            $query->where('plateforme_id', $request->plateforme);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->latest()->paginate(20)->withQueryString();
        
        $roles = Role::all();
        $plateformes = Plateforme::where('etat', true)->get();
        
        return view('support.admin.users', compact('users', 'roles', 'plateformes'));
    }

    /**
     * Formulaire de création d'utilisateur
     */
    public function createUser()
    {
        $roles = Role::all();
        $plateformes = Plateforme::where('etat', true)->get();
        
        return view('support.admin.users-create', compact('roles', 'plateformes'));
    }

    /**
     * Créer un utilisateur
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'plateforme_id' => 'required|exists:plateformes,id',
            'role_id' => 'required|exists:roles,id',
            'externe_id' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
            'etat' => 'nullable|boolean',
        ]);

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'plateforme_id' => $request->plateforme_id,
            'role_id' => $request->role_id,
            'externe_id' => $request->externe_id,
            'password' => Hash::make($request->password),
            'etat' => $request->etat ?? true,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Formulaire d'édition d'utilisateur
     */
    public function editUser(User $user)
    {
        $roles = Role::all();
        $plateformes = Plateforme::where('etat', true)->get();
        
        return view('support.admin.users-edit', compact('user', 'roles', 'plateformes'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'plateforme_id' => 'required|exists:plateformes,id',
            'role_id' => 'required|exists:roles,id',
            'externe_id' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'etat' => 'nullable|boolean',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'plateforme_id' => $request->plateforme_id,
            'role_id' => $request->role_id,
            'externe_id' => $request->externe_id,
            'etat' => $request->etat ?? $user->etat,
        ];

        // Mettre à jour le mot de passe seulement s'il est fourni
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(User $user)
    {
        // Empêcher la suppression de soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Liste des plateformes
     */
    public function plateformes()
    {
        $plateformes = Plateforme::withCount(['users', 'tickets'])->latest()->get();
        
        return view('support.admin.plateformes', compact('plateformes'));
    }

    /**
     * Formulaire de création de plateforme
     */
    public function createPlateforme()
    {
        return view('support.admin.plateformes.create');
    }

    /**
     * Créer une plateforme
     */
    public function storePlateforme(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:plateformes,code',
            'cle_api' => 'required|string|max:255',
            'secret_key' => 'required|string|max:255',
            'description' => 'nullable|string',
            'etat' => 'nullable|boolean',
            'couleur' => 'nullable|string|max:7',
        ]);

        Plateforme::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'cle_api' => $request->cle_api,
            'secret_key' => $request->secret_key,
            'description' => $request->description,
            'etat' => $request->etat ?? true,
            'couleur' => $request->couleur ?? '#3b82f6',
        ]);

        return redirect()->route('admin.plateformes')
            ->with('success', 'Plateforme créée avec succès');
    }

    /**
     * Formulaire d'édition de plateforme
     */
    public function editPlateforme(Plateforme $plateforme)
    {
        return view('support.admin.plateformes.edit', compact('plateforme'));
    }

    /**
     * Mettre à jour une plateforme
     */
    public function updatePlateforme(Request $request, Plateforme $plateforme)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'cle_api' => 'required|string|max:255',
            'secret_key' => 'required|string|max:255',
            'description' => 'nullable|string',
            'etat' => 'nullable|boolean',
            'couleur' => 'nullable|string|max:7',
        ]);

        $plateforme->update([
            'nom' => $request->nom,
            'cle_api' => $request->cle_api,
            'secret_key' => $request->secret_key,
            'description' => $request->description,
            'etat' => $request->etat ?? true,
            'couleur' => $request->couleur ?? '#3b82f6',
        ]);

        return redirect()->route('admin.plateformes')
            ->with('success', 'Plateforme mise à jour avec succès');
    }

    /**
     * Supprimer une plateforme
     */
    public function deletePlateforme(Plateforme $plateforme)
    {
        // Vérifier si des utilisateurs sont liés
        if ($plateforme->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une plateforme avec des utilisateurs liés');
        }

        $plateforme->delete();

        return redirect()->route('admin.plateformes')
            ->with('success', 'Plateforme supprimée avec succès');
    }

    /**
     * Paramètres système
     */
    public function settings()
    {
        return view('support.admin.settings');
    }

    /**
     * Mettre à jour les paramètres système
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_email' => 'required|email',
            'maintenance_mode' => 'nullable|boolean',
            'max_tickets_per_user' => 'nullable|integer|min:1',
        ]);

        // Ici, on pourrait stocker ces paramètres dans un fichier de config ou en base
        // Pour l'instant, on simule la mise à jour
        
        return back()->with('success', 'Paramètres mis à jour avec succès');
    }
}
