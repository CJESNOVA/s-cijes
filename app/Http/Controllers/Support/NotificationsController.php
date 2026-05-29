<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Afficher la page des notifications
     */
    public function page(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications()->with('notifiable');
        
        // Filtres
        if ($request->type) {
            $query->ofType($request->type);
        }
        
        if ($request->read !== null) {
            $query->where('read', $request->read);
        }
        
        $notifications = $query->latest()->paginate(20)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->notifications()->where('read', true)->count(),
        ];
        
        // Types disponibles pour les filtres
        $types = $user->notifications()
            ->select('type', DB::raw('count(*) as count'), DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('support.notifications.index', compact('notifications', 'stats', 'types'));
    }
    
    /**
     * Afficher la liste des notifications de l'utilisateur
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications()->with('notifiable');
        
        // Filtres
        if ($request->type) {
            $query->ofType($request->type);
        }
        
        if ($request->read !== null) {
            $query->where('read', $request->read);
        }
        
        $notifications = $query->paginate(20);
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }
    
    /**
     * Obtenir le compteur de notifications non lues
     */
    public function count(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'total_count' => $user->notifications()->count(),
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'message' => 'Notification marquée comme lue',
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        $user->unreadNotifications()->update([
            'read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Toutes les notifications marquées comme lues',
            'unread_count' => 0,
        ]);
    }
    
    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        $notification->delete();
        
        return response()->json([
            'message' => 'Notification supprimée',
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
    
    /**
     * Obtenir les notifications récentes pour le dropdown
     */
    public function recent(): JsonResponse
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->with('notifiable')
            ->latest()
            ->take(10)
            ->get();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }
    
    /**
     * Créer une notification (pour les tests et l'admin)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
        ]);
        
        $targetUser = User::findOrFail($request->user_id);
        
        $notification = Notification::createNotification(
            $targetUser,
            $request->type,
            $request->title,
            $request->message,
            null,
            $request->data ?? []
        );
        
        return response()->json([
            'message' => 'Notification créée avec succès',
            'notification' => $notification,
        ]);
    }
    
    /**
     * Vider toutes les notifications de l'utilisateur
     */
    public function clear(): JsonResponse
    {
        $user = Auth::user();
        
        $deleted = $user->notifications()->delete();
        
        return response()->json([
            'message' => 'Toutes les notifications ont été supprimées',
            'deleted_count' => $deleted,
            'unread_count' => 0,
        ]);
    }
}
