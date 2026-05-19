<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Plateforme;
use App\Models\TicketCategorie;
use App\Models\TicketPriorite;
use App\Models\Ticket;
use App\Models\TicketFichier;
use App\Models\TicketSatisfaction;
use App\Models\TicketHistorique;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketResolved;
use App\Mail\TicketAssigned;
use App\Mail\TicketMessageReceived;
use App\Mail\TicketSatisfactionReceived;

class TicketController extends Controller
{
    public function create()
    {
        // On récupère uniquement ce qui est nécessaire pour le formulaire
        $plateformes = Plateforme::where('etat', true)->get();
        $categories = TicketCategorie::all();
        $priorites = TicketPriorite::orderBy('niveau', 'asc')->get();

        return view('support.tickets.create', compact('plateformes', 'categories', 'priorites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'plateforme_id' => 'required|exists:plateformes,id',
            'module_id' => 'required|exists:modules,id',
            'categorie_id' => 'required|exists:ticket_categories,id',
            'priorite_id' => 'required|exists:ticket_priorites,id',
            'attachments.*' => 'nullable|file|max:5120', // Max 5Mo par fichier
        ]);

        // Génération automatique de la référence (ex: TCK-2024-XXXX)
        $reference = 'TCK-' . date('Y') . '-' . strtoupper(Str::random(6));

        // 1. Création du ticket
        $ticket = Ticket::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'plateforme_id' => $validated['plateforme_id'],
            'module_id' => $validated['module_id'],
            'categorie_id' => $validated['categorie_id'],
            'priorite_id' => $validated['priorite_id'],
            'reference' => $reference,
            'user_id' => Auth::id(),
            'statut_id' => 1, // Statut "Ouvert" par défaut
            'date_ouverture' => now(),
        ]);

        // 2. Gestion des fichiers joints
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');

                TicketFichier::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'nom' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'extension' => $file->getClientOriginalExtension(),
                    'taille' => $file->getSize(),
                ]);
            }
        }

        // 3. Créer les notifications
        $this->createTicketNotifications($ticket);

        // 4. Créer l'entrée d'historique
        TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Ticket créé',
            'details' => [
                'reference' => $ticket->reference,
                'titre' => $ticket->titre,
                'priorite' => $ticket->priorite->nom,
                'plateforme' => $ticket->plateforme->nom,
            ],
        ]);

        return redirect()->route('dashboard')->with('success', "Ticket {$reference} créé avec succès.");
    }

    public function show(Ticket $ticket)
    {
        // Charger toutes les relations nécessaires
        $ticket->load([
            'user', 
            'technicien', 
            'statut', 
            'priorite', 
            'categorie', 
            'plateforme', 
            'module', 
            'fichiers', 
            'messages.user', // On charge aussi l'auteur de chaque message
            'satisfaction' // Charger la satisfaction si elle existe
        ]);

        // Vérification de sécurité : Seul le demandeur, son technicien ou un admin peut voir
        // if (auth()->id() !== $ticket->user_id && auth()->id() !== $ticket->technicien_id) {
        //    abort(403);
        // }

        // Données pour le formulaire
        $statuts = \App\Models\TicketStatut::all();
        $priorites = \App\Models\TicketPriorite::all();

        return view('support.tickets.show', compact('ticket', 'statuts', 'priorites'));
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'technicien', 'statut', 'priorite', 'plateforme', 'module']);

        // Filtrage par recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Filtrage par plateforme
        if ($request->filled('plateforme_id')) {
            $query->where('plateforme_id', $request->input('plateforme_id'));
        }

        // Filtrage par module
        if ($request->filled('module_id')) {
            $query->where('module_id', $request->input('module_id'));
        }

        // Filtrage par statut
        if ($request->filled('statut_id')) {
            $query->where('statut_id', $request->input('statut_id'));
        }

        // Filtrage par priorité
        if ($request->filled('priorite_id')) {
            $query->where('priorite_id', $request->input('priorite_id'));
        }

        // Tri par date de création (plus récent d'abord)
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        // Données pour les filtres
        $plateformes = \App\Models\Plateforme::where('etat', true)->get();
        $modules = \App\Models\Module::where('etat', true)->get();
        $statuts = \App\Models\TicketStatut::all();
        $priorites = \App\Models\TicketPriorite::all();

        return view('support.tickets.index', compact('tickets', 'plateformes', 'modules', 'statuts', 'priorites'));
    }

    /**
     * Créer les notifications lors de la création d'un ticket
     */
    private function createTicketNotifications(Ticket $ticket)
    {
        // Notification pour le créateur du ticket
        Notification::createNotification(
            $ticket->user,
            'ticket_created',
            'Ticket créé',
            "Votre ticket {$ticket->reference} a été créé avec succès",
            $ticket,
            [
                'ticket_id' => $ticket->id,
                'reference' => $ticket->reference,
                'titre' => $ticket->titre,
                'priorite' => $ticket->priorite->nom,
            ]
        );

        // Notification pour les techniciens et superviseurs (si priorité haute)
        if ($ticket->priorite->niveau >= 3) { // Priorité haute/urgente
            $techniciens = User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Superviseur', 'Administrateur']);
            })->get();

            foreach ($techniciens as $technicien) {
                Notification::createNotification(
                    $technicien,
                    'urgent_ticket',
                    'Ticket urgent',
                    "Un ticket urgent {$ticket->reference} a été créé par {$ticket->user->nom} {$ticket->user->prenom}",
                    $ticket,
                    [
                        'ticket_id' => $ticket->id,
                        'reference' => $ticket->reference,
                        'titre' => $ticket->titre,
                        'priorite' => $ticket->priorite->nom,
                        'demandeur' => $ticket->user->nom . ' ' . $ticket->user->prenom,
                    ]
                );
            }
        }

        // Notification pour les superviseurs (tous les tickets)
        $superviseurs = User::whereHas('role', function($query) {
            $query->whereIn('titre', ['Superviseur', 'Administrateur']);
        })->get();

        foreach ($superviseurs as $superviseur) {
            Notification::createNotification(
                $superviseur,
                'ticket_created',
                'Nouveau ticket',
                "Un nouveau ticket {$ticket->reference} a été créé",
                $ticket,
                [
                    'ticket_id' => $ticket->id,
                    'reference' => $ticket->reference,
                    'titre' => $ticket->titre,
                    'demandeur' => $ticket->user->nom . ' ' . $ticket->user->prenom,
                    'plateforme' => $ticket->plateforme->nom,
                ]
            );
        }
    }

    /**
     * Marquer un ticket comme résolu
     */
    public function resolve(Ticket $ticket)
    {
        // Vérifier les permissions - seuls les demandeurs ne peuvent pas résoudre
        if (in_array(Auth::user()->role->titre, ['Demandeur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        if ($ticket->statut->nom === 'Résolu') {
            return response()->json(['error' => 'Le ticket est déjà résolu'], 400);
        }

        // Mettre à jour le statut
        $resolvedStatus = \App\Models\TicketStatut::where('nom', 'Résolu')->first();
        if ($resolvedStatus) {
            $ticket->update([
                'statut_id' => $resolvedStatus->id,
                'resolu_par' => Auth::id(),
                'date_resolution' => now(),
            ]);

            // Ajouter un message système
            $ticket->messages()->create([
                'message' => 'Ticket marqué comme résolu par ' . Auth::user()->nom . ' ' . Auth::user()->prenom,
                'user_id' => Auth::id(),
                'interne' => true,
                'system_message' => true,
            ]);

            // Créer l'entrée d'historique
            TicketHistorique::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'Statut changé',
                'details' => [
                    'old_status' => $ticket->getOriginal('statut_id') ? \App\Models\TicketStatut::find($ticket->getOriginal('statut_id'))->nom : 'Inconnu',
                    'new_status' => 'Résolu',
                    'resolved_by' => Auth::user()->nom . ' ' . Auth::user()->prenom,
                ],
            ]);

            // Notifier le demandeur
            Notification::createNotification(
                $ticket->user,
                'ticket_resolved',
                'Ticket résolu',
                "Votre ticket {$ticket->reference} a été résolu",
                $ticket,
                [
                    'ticket_id' => $ticket->id,
                    'reference' => $ticket->reference,
                    'titre' => $ticket->titre,
                    'resolu_par' => Auth::user()->nom . ' ' . Auth::user()->prenom,
                ]
            );

            // Envoyer un email au demandeur
            try {
                Mail::to($ticket->user->email)->send(new TicketResolved($ticket, Auth::user()->nom . ' ' . Auth::user()->prenom));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email ticket résolu: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket marqué comme résolu avec succès',
                'status' => 'Résolu',
                'resolved_by' => Auth::user()->nom . ' ' . Auth::user()->prenom,
                'resolved_at' => now()->format('d/m/Y H:i'),
            ]);
        }

        return response()->json(['error' => 'Statut "Résolu" non trouvé'], 500);
    }

    /**
     * Mettre à jour la priorité d'un ticket
     */
    public function updatePriority(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions - seuls les demandeurs ne peuvent pas modifier
        if (in_array(Auth::user()->role->titre, ['Demandeur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'priority_id' => 'required|exists:ticket_priorites,id',
        ]);

        $oldPriority = $ticket->priorite->nom;
        $newPriority = \App\Models\TicketPriorite::find($request->priority_id);

        $ticket->update(['priorite_id' => $request->priority_id]);

        // Ajouter un message système
        $ticket->messages()->create([
            'message' => "Priorité changée de '{$oldPriority}' à '{$newPriority->nom}' par " . Auth::user()->nom . ' ' . Auth::user()->prenom,
            'user_id' => Auth::id(),
            'interne' => true,
            'system_message' => true,
        ]);

        // Créer l'entrée d'historique
        TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Priorité changée',
            'details' => [
                'old_priority' => $oldPriority,
                'new_priority' => $newPriority->nom,
                'changed_by' => Auth::user()->nom . ' ' . Auth::user()->prenom,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Priorité mise à jour avec succès',
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority->nom,
        ]);
    }

    /**
     * Mettre à jour le statut d'un ticket
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions - seuls les demandeurs ne peuvent pas modifier
        if (in_array(Auth::user()->role->titre, ['Demandeur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'statut_id' => 'required|exists:ticket_statuts,id',
        ]);

        $oldStatus = $ticket->statut->nom;
        $newStatus = \App\Models\TicketStatut::find($request->statut_id);

        $ticket->update(['statut_id' => $request->statut_id]);

        // Ajouter un message système
        $ticket->messages()->create([
            'message' => "Statut changé de '{$oldStatus}' à '{$newStatus->nom}' par " . Auth::user()->nom . ' ' . Auth::user()->prenom,
            'user_id' => Auth::id(),
            'interne' => true,
            'system_message' => true,
        ]);

        // Créer l'entrée d'historique
        TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Statut changé',
            'details' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus->nom,
                'changed_by' => Auth::user()->nom . ' ' . Auth::user()->prenom,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'old_status' => $oldStatus,
            'new_status' => $newStatus->nom,
        ]);
    }

    /**
     * Fusionner un ticket avec un autre
     */
    public function mergeTicket(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'target_ticket_id' => 'required|exists:tickets,id|different:' . $ticket->id,
        ]);

        $targetTicket = Ticket::find($request->target_ticket_id);

        // Déplacer tous les messages du ticket source vers le ticket cible
        $ticket->messages()->update(['ticket_id' => $targetTicket->id]);

        // Marquer le ticket source comme fusionné
        $mergedStatus = \App\Models\TicketStatut::where('nom', 'Fusionné')->first();
        if ($mergedStatus) {
            $ticket->update([
                'statut_id' => $mergedStatus->id,
                'fusionne_avec' => $targetTicket->id,
            ]);
        }

        // Ajouter un message système dans les deux tickets
        $mergeMessage = "Ticket fusionné avec {$targetTicket->reference} par " . Auth::user()->nom . ' ' . Auth::user()->prenom;
        
        $ticket->messages()->create([
            'message' => $mergeMessage,
            'user_id' => Auth::id(),
            'interne' => true,
            'system_message' => true,
        ]);

        $targetTicket->messages()->create([
            'message' => "Ticket {$ticket->reference} fusionné avec ce ticket par " . Auth::user()->nom . ' ' . Auth::user()->prenom,
            'user_id' => Auth::id(),
            'interne' => true,
            'system_message' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket fusionné avec succès',
            'target_ticket' => $targetTicket->reference,
        ]);
    }

    /**
     * Supprimer un ticket
     */
    public function destroy(Ticket $ticket)
    {
        // Seuls les administrateurs peuvent supprimer des tickets
        if (Auth::user()->role->titre !== 'Administrateur') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Ajouter un message de suppression dans les logs
        \Log::info('Ticket supprimé', [
            'ticket_id' => $ticket->id,
            'reference' => $ticket->reference,
            'deleted_by' => Auth::id(),
            'deleted_by_name' => Auth::user()->nom . ' ' . Auth::user()->prenom,
        ]);

        // Supprimer les messages associés
        $ticket->messages()->delete();

        // Supprimer le ticket
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket supprimé avec succès',
            'redirect_to' => route('tickets.index'),
        ]);
    }

    /**
     * Joindre un fichier à un ticket
     */
    public function attachFile(Request $request, Ticket $ticket)
    {
        // Vérifier les permissions
        if (in_array(Auth::user()->role->titre, ['Demandeur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'file.*' => 'required|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();
                
                $filePath = $file->store('tickets/attachments');
                
                // Créer l'enregistrement dans la base de données
                $attachment = TicketFichier::create([
                    'ticket_id' => $ticket->id,
                    'nom' => $originalName,
                    'chemin' => $filePath,
                    'extension' => $file->getClientOriginalExtension(),
                    'taille' => $file->getSize(),
                    'user_id' => Auth::id(),
                ]);

            // Ajouter un message système si nécessaire
            $ticket->messages()->create([
                'message' => 'Fichier joint par ' . Auth::user()->nom . ' ' . Auth::user()->prenom . ': ' . $originalName,
                'user_id' => Auth::id(),
                'interne' => false,
                'attachment_id' => $attachment->id,
            ]);

            return response()->json([
                    'success' => true,
                    'message' => 'Fichier joint avec succès',
                    'attachment' => [
                        'id' => $attachment->id,
                        'name' => $originalName,
                        'size' => $attachment->taille_formatee,
                        'url' => $attachment->url,
                        'extension' => $attachment->extension,
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Erreur lors de l\'upload: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json(['error' => 'Aucun fichier fourni'], 400);
    }

    /**
     * Supprimer un fichier joint
     */
    public function removeAttachment(Ticket $ticket, TicketFichier $attachment)
    {
        // Vérifier les permissions
        if (in_array(Auth::user()->role->titre, ['Demandeur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le fichier appartient bien au ticket
        if ($attachment->ticket_id !== $ticket->id) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }

        // Supprimer le fichier
        Storage::delete($attachment->chemin);
        $attachment->delete();

        // Ajouter un message système
        $ticket->messages()->create([
            'message' => 'Fichier supprimé par ' . Auth::user()->nom . ' ' . Auth::user()->prenom . ': ' . $attachment->nom_original,
            'user_id' => Auth::id(),
            'interne' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fichier supprimé avec succès',
        ]);
    }

    /**
     * Enregistrer la satisfaction du demandeur
     */
    public function submitSatisfaction(Request $request, Ticket $ticket)
    {
        // Vérifier que l'utilisateur est le demandeur du ticket
        if (Auth::id() !== $ticket->user_id) {
            return response()->json(['error' => 'Seul le demandeur peut donner sa satisfaction'], 403);
        }

        // Vérifier que le ticket est résolu
        if ($ticket->statut->nom !== 'Résolu') {
            return response()->json(['error' => 'La satisfaction ne peut être donnée que pour un ticket résolu'], 400);
        }

        // Vérifier qu'une satisfaction n'existe pas déjà
        if ($ticket->satisfaction) {
            return response()->json(['error' => 'Une satisfaction a déjà été enregistrée pour ce ticket'], 400);
        }

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Créer l'enregistrement de satisfaction
        $satisfaction = TicketSatisfaction::create([
            'ticket_id' => $ticket->id,
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
        ]);

        // Notifier les techniciens et superviseurs
        $this->notifySatisfaction($ticket, $satisfaction);

        // Créer l'entrée d'historique
        TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Satisfaction enregistrée',
            'details' => [
                'satisfaction_note' => $satisfaction->note,
                'commentaire' => $satisfaction->commentaire,
                'user' => Auth::user()->nom . ' ' . Auth::user()->prenom,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre feedback ! Votre satisfaction a été enregistrée.',
            'satisfaction' => $satisfaction,
        ]);
    }

    /**
     * Notifier les techniciens de la satisfaction
     */
    private function notifySatisfaction(Ticket $ticket, TicketSatisfaction $satisfaction)
    {
        $message = "Satisfaction enregistrée pour le ticket {$ticket->reference}: {$satisfaction->note}/5";
        if ($satisfaction->commentaire) {
            $message .= " - \"{$satisfaction->commentaire}\"";
        }

        // Notifier le technicien assigné
        if ($ticket->technicien) {
            Notification::createNotification(
                $ticket->technicien,
                'satisfaction_recorded',
                'Satisfaction client enregistrée',
                $message,
                $ticket,
                ['note' => $satisfaction->note, 'commentaire' => $satisfaction->commentaire]
            );

            // Envoyer un email au technicien
            try {
                Mail::to($ticket->technicien->email)->send(new TicketSatisfactionReceived($ticket, $satisfaction));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email satisfaction technicien: ' . $e->getMessage());
            }
        }

        // Notifier les superviseurs
        $superviseurs = User::whereHas('role', function($query) {
            $query->whereIn('titre', ['Superviseur', 'Administrateur']);
        })->get();

        foreach ($superviseurs as $superviseur) {
            Notification::createNotification(
                $superviseur,
                'satisfaction_recorded',
                'Satisfaction client enregistrée',
                $message,
                $ticket,
                ['note' => $satisfaction->note, 'commentaire' => $satisfaction->commentaire]
            );

            // Envoyer un email aux superviseurs
            try {
                Mail::to($superviseur->email)->send(new TicketSatisfactionReceived($ticket, $satisfaction));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email satisfaction superviseur: ' . $e->getMessage());
            }
        }
    }

    }
