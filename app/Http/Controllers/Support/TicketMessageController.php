<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketStatut;
use App\Models\TicketFichier;
use App\Models\TicketHistorique;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketMessageReceived;
use App\Mail\TicketAssigned;

class TicketMessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        // Vérifier si c'est une requête AJAX (avec fichiers)
        if ($request->ajax()) {
            return $this->storeWithFiles($request, $ticket);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:2',
            'interne' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $isStaff = $user->role->titre !== 'Demandeur';

        // 1. Logique d'assignation automatique
        if ($isStaff && is_null($ticket->technicien_id)) {
            $ticket->update([
                'technicien_id' => $user->id,
                'statut_id' => 3, // ID du statut 'En cours'
            ]);
            
            // Créer une entrée dans l'historique pour l'assignation
            TicketHistorique::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => 'Ticket assigné',
                'details' => [
                    'assigned_to' => $user->nom . ' ' . $user->prenom,
                    'status_changed' => 'En cours',
                    'auto_assigned' => true,
                ],
            ]);
        }

        // 2. Création du message
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'message'   => $validated['message'],
            'interne'   => $request->has('interne') && $isStaff,
        ]);

        // 3. Créer l'entrée d'historique pour le message
        TicketHistorique::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => $request->has('interne') && $isStaff ? 'Note interne ajoutée' : 'Message ajouté',
            'details' => [
                'message_preview' => substr($validated['message'], 0, 100),
                'is_internal' => $request->has('interne') && $isStaff,
                'author' => $user->nom . ' ' . $user->prenom,
            ],
        ]);

        // 4. Créer les notifications
        $this->createMessageNotifications($ticket, $user, $validated['message'], $isStaff, $request->has('interne'));

        $message = $isStaff && is_null($ticket->technicien_id) 
            ? 'Message envoyé et ticket assigné.' 
            : ($request->has('interne') && $isStaff ? 'Note interne ajoutée.' : 'Message envoyé.');

        return back()->with('success', $message);
    }

    /**
     * Stocker un message avec des fichiers
     */
    public function storeWithFiles(Request $request, Ticket $ticket)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|min:2',
                'interne' => 'nullable|boolean',
                'files' => 'nullable|array',
                'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar',
            ]);

            $user = auth()->user();
            $isStaff = $user->role->titre !== 'Demandeur';

            // 1. Logique d'assignation automatique
            if ($isStaff && is_null($ticket->technicien_id)) {
                $ticket->update([
                    'technicien_id' => $user->id,
                    'statut_id' => 3,
                ]);
            }

            // 2. Création du message
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'message'   => $validated['message'],
                'interne'   => $request->has('interne') && $isStaff,
            ]);

            // 3. Traitement des fichiers
            $attachments = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filePath = $file->store('tickets/attachments');
                    
                    $attachment = TicketFichier::create([
                        'ticket_id' => $ticket->id,
                        'nom' => $originalName,
                        'chemin' => $filePath,
                        'extension' => $file->getClientOriginalExtension(),
                        'taille' => $file->getSize(),
                        'user_id' => $user->id,
                    ]);

                    $attachments[] = [
                        'id' => $attachment->id,
                        'name' => $originalName,
                        'size' => $attachment->taille_formatee,
                        'url' => $attachment->url,
                        'extension' => $attachment->extension,
                    ];
                }
            }

            // 4. Créer les notifications
            $this->createMessageNotifications($ticket, $user, $validated['message'], $isStaff, $request->has('interne'));

            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès',
                'attachments' => $attachments,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Créer les notifications lors de l'envoi d'un message
     */
    private function createMessageNotifications(Ticket $ticket, User $sender, string $message, bool $isStaff, bool $isInternal)
    {
        // Ne pas notifier pour les messages internes
        if ($isInternal) {
            return;
        }

        // Si c'est un technicien qui répond, notifier le demandeur
        if ($isStaff && $ticket->user_id !== $sender->id) {
            Notification::createNotification(
                $ticket->user,
                'message_added',
                'Nouvelle réponse',
                "Vous avez une nouvelle réponse pour votre ticket {$ticket->reference}",
                $ticket,
                [
                    'ticket_id' => $ticket->id,
                    'reference' => $ticket->reference,
                    'sender' => $sender->nom . ' ' . $sender->prenom,
                    'message_preview' => substr($message, 0, 100),
                ]
            );

            // Envoyer un email au demandeur
            try {
                $ticketMessage = TicketMessage::where('ticket_id', $ticket->id)
                    ->where('user_id', $sender->id)
                    ->where('message', $message)
                    ->latest()
                    ->first();
                Mail::to($ticket->user->email)->send(new TicketMessageReceived($ticket, $ticketMessage));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email message reçu: ' . $e->getMessage());
            }
        }

        // Si c'est un demandeur qui répond, notifier le technicien assigné
        if (!$isStaff && $ticket->technicien_id && $ticket->technicien_id !== $sender->id) {
            Notification::createNotification(
                $ticket->technicien,
                'message_added',
                'Nouveau message',
                "Nouveau message de {$ticket->user->nom} {$ticket->user->prenom} pour le ticket {$ticket->reference}",
                $ticket,
                [
                    'ticket_id' => $ticket->id,
                    'reference' => $ticket->reference,
                    'sender' => $ticket->user->nom . ' ' . $ticket->user->prenom,
                    'message_preview' => substr($message, 0, 100),
                ]
            );
        }

        // Si le ticket n'a pas de technicien et qu'un demandeur répond, notifier tous les techniciens
        if (!$isStaff && !$ticket->technicien_id) {
            $techniciens = User::whereHas('role', function($query) {
                $query->whereIn('titre', ['Technicien', 'Superviseur', 'Administrateur']);
            })->where('id', '!=', $sender->id)->get();

            foreach ($techniciens as $technicien) {
                Notification::createNotification(
                    $technicien,
                    'message_added',
                    'Nouveau message client',
                    "Nouveau message pour le ticket non assigné {$ticket->reference}",
                    $ticket,
                    [
                        'ticket_id' => $ticket->id,
                        'reference' => $ticket->reference,
                        'sender' => $ticket->user->nom . ' ' . $ticket->user->prenom,
                        'message_preview' => substr($message, 0, 100),
                    ]
                );
            }
        }
    }
}
