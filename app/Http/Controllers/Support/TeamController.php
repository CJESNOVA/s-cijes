<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Afficher la liste des équipes du superviseur
     */
    public function index()
    {
        // Seuls les superviseurs et administrateurs peuvent voir les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        $teams = Team::with(['supervisor', 'activeMembers.user'])
            ->when(Auth::user()->role->titre === 'Superviseur', function($query) {
                $query->where('supervisor_id', Auth::id());
            })
            ->active()
            ->orderBy('name')
            ->paginate(10);

        return view('support.teams.index', compact('teams'));
    }

    /**
     * Afficher le formulaire de création d'équipe
     */
    public function create()
    {
        // Seuls les superviseurs et administrateurs peuvent créer des équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        $techniciens = User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Technicien');
        })->where('active', true)->orderBy('nom')->get();

        $superviseurs = User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Superviseur');
        })->where('active', true)->orderBy('nom')->get();

        return view('support.teams.create', compact('techniciens', 'superviseurs'));
    }

    /**
     * Enregistrer une nouvelle équipe
     */
    public function store(Request $request)
    {
        // Seuls les superviseurs et administrateurs peuvent créer des équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'supervisor_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'supervisor_id' => $request->supervisor_id,
            'active' => true,
        ]);

        // Ajouter les membres
        if ($request->has('members')) {
            foreach ($request->members as $memberId) {
                $role = ($memberId == $request->leader_id) ? 'leader' : 'member';
                $team->addMember(User::find($memberId), $role);
            }
        }

        return redirect()
            ->route('teams.index')
            ->with('success', 'Équipe créée avec succès');
    }

    /**
     * Afficher les détails d'une équipe
     */
    public function show(Team $team)
    {
        // Seuls les superviseurs et administrateurs peuvent voir les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $team->load(['supervisor', 'activeMembers.user.role']);

        return view('support.teams.show', compact('team'));
    }

    /**
     * Afficher le formulaire d'édition d'équipe
     */
    public function edit(Team $team)
    {
        // Seuls les superviseurs et administrateurs peuvent modifier les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $team->load(['activeMembers.user']);

        $techniciens = User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Technicien');
        })->where('active', true)->orderBy('nom')->get();

        $superviseurs = User::where('role_id', function($query) {
            $query->select('id')->from('roles')->where('titre', 'Superviseur');
        })->where('active', true)->orderBy('nom')->get();

        return view('support.teams.edit', compact('team', 'techniciens', 'superviseurs'));
    }

    /**
     * Mettre à jour une équipe
     */
    public function update(Request $request, Team $team)
    {
        // Seuls les superviseurs et administrateurs peuvent modifier les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'supervisor_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'supervisor_id' => $request->supervisor_id,
        ]);

        // Mettre à jour les membres
        $currentMembers = $team->activeMembers()->pluck('user_id')->toArray();
        $newMembers = $request->members ?? [];

        // Désactiver les membres qui ne sont plus dans la liste
        foreach ($currentMembers as $memberId) {
            if (!in_array($memberId, $newMembers)) {
                $team->removeMember(User::find($memberId));
            }
        }

        // Ajouter les nouveaux membres
        foreach ($newMembers as $memberId) {
            $role = ($memberId == $request->leader_id) ? 'leader' : 'member';
            $team->addMember(User::find($memberId), $role);
        }

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Équipe mise à jour avec succès');
    }

    /**
     * Supprimer une équipe
     */
    public function destroy(Team $team)
    {
        // Seuls les administrateurs peuvent supprimer des équipes
        if (Auth::user()->role->titre !== 'Administrateur') {
            abort(403, 'Accès non autorisé');
        }

        $team->update(['active' => false]);

        return redirect()
            ->route('teams.index')
            ->with('success', 'Équipe supprimée avec succès');
    }

    /**
     * API pour ajouter un membre à une équipe
     */
    public function addMember(Request $request, Team $team)
    {
        // Seuls les superviseurs et administrateurs peuvent modifier les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => ['required', Rule::in(['leader', 'member'])],
        ]);

        $user = User::find($request->user_id);
        $team->addMember($user, $request->role);

        return response()->json([
            'success' => true,
            'message' => 'Membre ajouté avec succès',
            'member' => [
                'id' => $user->id,
                'name' => $user->nom . ' ' . $user->prenom,
                'role' => $request->role,
                'joined_at' => now()->format('d/m/Y'),
            ]
        ]);
    }

    /**
     * API pour supprimer un membre d'une équipe
     */
    public function removeMember(Request $request, Team $team, User $user)
    {
        // Seuls les superviseurs et administrateurs peuvent modifier les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $team->removeMember($user);

        return response()->json([
            'success' => true,
            'message' => 'Membre supprimé avec succès',
        ]);
    }

    /**
     * API pour promouvoir un membre en leader
     */
    public function promoteMember(Request $request, Team $team, User $user)
    {
        // Seuls les superviseurs et administrateurs peuvent modifier les équipes
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions
        if (Auth::user()->role->titre === 'Superviseur' && $team->supervisor_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $team->setLeader($user);

        return response()->json([
            'success' => true,
            'message' => 'Membre promu avec succès',
        ]);
    }

    /**
     * Dashboard des équipes
     */
    public function dashboard()
    {
        // Seuls les superviseurs et administrateurs peuvent voir le dashboard
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }

        $query = Team::active();
        
        if (Auth::user()->role->titre === 'Superviseur') {
            $query->where('supervisor_id', Auth::id());
        }

        $stats = [
            'total_teams' => $query->count(),
            'total_members' => $query->withCount('activeMembers')->get()->sum('active_members_count'),
            'total_leaders' => $query->whereHas('activeMembers', function($q) {
                $q->where('role', 'leader');
            })->count(),
            'recent_teams' => $query->orderBy('created_at', 'desc')->take(5)->get(),
        ];

        $teams = $query->with(['supervisor', 'activeMembers.user'])
            ->orderBy('name')
            ->paginate(10);

        return view('support.teams.dashboard', compact('stats', 'teams'));
    }
}
