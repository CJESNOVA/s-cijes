<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KnowledgeBaseController extends Controller
{
    /**
     * Afficher la page principale de la base de connaissances
     */
    public function index(Request $request)
    {
        // Tous les utilisateurs authentifiés peuvent accéder à la base de connaissances
        
        $search = $request->get('search', '');
        $category_id = $request->get('category_id', '');
        $sort = $request->get('sort', 'updated_at');
        $order = $request->get('order', 'desc');
        
        $query = KnowledgeBase::with(['category', 'author', 'updater'])
            ->where('published', true);
        
        // Filtrage par recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par catégorie
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        
        // Tri
        $allowedSorts = ['title', 'created_at', 'updated_at', 'views_count', 'helpful_count'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }
        
        $articles = $query->paginate(12);
        $categories = KnowledgeBaseCategory::where('active', true)
            ->withCount(['articles' => function($query) {
                $query->where('published', true);
            }])
            ->orderBy('name')
            ->get();
        
        $recentArticles = KnowledgeBase::where('published', true)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'updated_at']);
        
        $popularArticles = KnowledgeBase::where('published', true)
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'views_count']);
        
        return view('support.knowledge-base.index', compact(
            'articles',
            'categories',
            'recentArticles',
            'popularArticles',
            'search',
            'category_id',
            'sort',
            'order'
        ));
    }
    
    /**
     * Afficher un article spécifique
     */
    public function show(KnowledgeBase $article)
    {
        // Tous les utilisateurs authentifiés peuvent accéder aux articles publiés
        
        if (!$article->published && $article->author_id !== Auth::id()) {
            abort(404);
        }
        
        // Incrémenter le compteur de vues
        $article->increment('views_count');
        
        // Articles liés
        $relatedArticles = KnowledgeBase::where('published', true)
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'excerpt']);
        
        // Articles similaires par tags
        $articleTags = explode(',', $article->tags);
        $similarArticles = collect();
        
        if (count($articleTags) > 0) {
            $similarArticles = KnowledgeBase::where('published', true)
                ->where('id', '!=', $article->id)
                ->where(function($query) use ($articleTags) {
                    foreach ($articleTags as $tag) {
                        $query->orWhere('tags', 'like', "%".trim($tag)."%");
                    }
                })
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get(['id', 'title', 'excerpt']);
        }
        
        return view('support.knowledge-base.show', compact(
            'article',
            'relatedArticles',
            'similarArticles'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'article
     */
    public function create()
    {
        // Seuls les administrateurs et superviseurs peuvent créer des articles
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $categories = KnowledgeBaseCategory::where('active', true)
            ->orderBy('name')
            ->get();
        
        return view('support.knowledge-base.create', compact('categories'));
    }
    
    /**
     * Enregistrer un nouvel article
     */
    public function store(Request $request)
    {
        // Seuls les administrateurs et superviseurs peuvent créer des articles
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:knowledge_base_categories,id',
            'tags' => 'nullable|string|max:255',
            'published' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:10240',
        ]);
        
        $article = KnowledgeBase::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 200),
            'category_id' => $request->category_id,
            'tags' => $request->tags,
            'author_id' => Auth::id(),
            'updater_id' => Auth::id(),
            'published' => $request->has('published'),
            'published_at' => $request->has('published') ? now() : null,
        ]);
        
        // Gérer l'attachement
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('knowledge-base/attachments', $filename, 'public');
            $article->update(['attachment_path' => $path]);
        }
        
        return redirect()
            ->route('knowledge-base.show', $article)
            ->with('success', 'Article créé avec succès');
    }
    
    /**
     * Afficher le formulaire d'édition d'article
     */
    public function edit(KnowledgeBase $article)
    {
        // Seuls les administrateurs, superviseurs, ou l'auteur peuvent éditer
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur']) && 
            $article->author_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }
        
        $categories = KnowledgeBaseCategory::where('active', true)
            ->orderBy('name')
            ->get();
        
        return view('support.knowledge-base.edit', compact('article', 'categories'));
    }
    
    /**
     * Mettre à jour un article
     */
    public function update(Request $request, KnowledgeBase $article)
    {
        // Seuls les administrateurs, superviseurs, ou l'auteur peuvent éditer
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur']) && 
            $article->author_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:knowledge_base_categories,id',
            'tags' => 'nullable|string|max:255',
            'published' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:10240',
        ]);
        
        $wasPublished = $article->published;
        
        $article->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 200),
            'category_id' => $request->category_id,
            'tags' => $request->tags,
            'updater_id' => Auth::id(),
            'published' => $request->has('published'),
            'published_at' => !$wasPublished && $request->has('published') ? now() : $article->published_at,
        ]);
        
        // Gérer l'attachement
        if ($request->hasFile('attachment')) {
            // Supprimer l'ancien fichier s'il existe
            if ($article->attachment_path) {
                Storage::disk('public')->delete($article->attachment_path);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('knowledge-base/attachments', $filename, 'public');
            $article->update(['attachment_path' => $path]);
        }
        
        return redirect()
            ->route('knowledge-base.show', $article)
            ->with('success', 'Article mis à jour avec succès');
    }
    
    /**
     * Supprimer un article
     */
    public function destroy(KnowledgeBase $article)
    {
        // Seuls les administrateurs peuvent supprimer des articles
        if (Auth::user()->role->titre !== 'Administrateur') {
            abort(403, 'Accès non autorisé');
        }
        
        // Supprimer l'attachement s'il existe
        if ($article->attachment_path) {
            Storage::disk('public')->delete($article->attachment_path);
        }
        
        $article->delete();
        
        return redirect()
            ->route('knowledge-base.index')
            ->with('success', 'Article supprimé avec succès');
    }
    
    /**
     * Notifier qu'un article est utile
     */
    public function markHelpful(KnowledgeBase $article)
    {
        // Tous les utilisateurs authentifiés peuvent voter
        
        $article->increment('helpful_count');
        
        return response()->json([
            'success' => true,
            'count' => $article->fresh()->helpful_count
        ]);
    }
    
    /**
     * Notifier qu'un article n'est pas utile
     */
    public function markNotHelpful(KnowledgeBase $article)
    {
        // Tous les utilisateurs authentifiés peuvent voter
        
        $article->increment('not_helpful_count');
        
        return response()->json([
            'success' => true,
            'count' => $article->fresh()->not_helpful_count
        ]);
    }
    
    /**
     * API pour la recherche en temps réel
     */
    public function search(Request $request)
    {
        // Tous les utilisateurs authentifiés peuvent rechercher
        
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $articles = KnowledgeBase::where('published', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('tags', 'like', "%{$query}%");
            })
            ->with(['category'])
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'excerpt', 'slug', 'category_id']);
        
        return response()->json($articles);
    }
    
    /**
     * Dashboard de la base de connaissances (pour administrateurs)
     */
    public function dashboard()
    {
        // Seuls les administrateurs et superviseurs peuvent accéder au dashboard
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            abort(403, 'Accès non autorisé');
        }
        
        $stats = [
            'total_articles' => KnowledgeBase::count(),
            'published_articles' => KnowledgeBase::where('published', true)->count(),
            'draft_articles' => KnowledgeBase::where('published', false)->count(),
            'total_views' => KnowledgeBase::sum('views_count'),
            'total_helpful' => KnowledgeBase::sum('helpful_count'),
            'categories_count' => KnowledgeBaseCategory::where('active', true)->count(),
        ];
        
        $recentActivity = KnowledgeBase::with(['author', 'category'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $popularArticles = KnowledgeBase::where('published', true)
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();
        
        $categoryStats = KnowledgeBaseCategory::where('active', true)
            ->withCount(['articles' => function($query) {
                $query->where('published', true);
            }])
            ->orderBy('articles_count', 'desc')
            ->get();
        
        return view('support.knowledge-base.dashboard', compact(
            'stats',
            'recentActivity',
            'popularArticles',
            'categoryStats'
        ));
    }

    /**
     * Joindre un fichier à un article
     */
    public function attachFile(Request $request, KnowledgeBase $article)
    {
        // Vérifier les permissions
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'file.*' => 'required|mimes:' . implode(',', KnowledgeBaseAttachment::getAllowedMimeTypes()),
            'description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();
            
            $filePath = $file->store(KnowledgeBaseAttachment::getStoragePath());
            
            // Créer l'enregistrement dans la base de données
            $attachment = KnowledgeBaseAttachment::create([
                'article_id' => $article->id,
                'nom_fichier' => $fileName,
                'nom_original' => $originalName,
                'chemin' => $filePath,
                'mime_type' => $file->getMimeType(),
                'taille' => $file->getSize(),
                'description' => $request->description,
                'is_primary' => $article->attachments()->count() === 0, // Premier fichier = primaire
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fichier joint avec succès',
                'attachment' => [
                    'id' => $attachment->id,
                    'name' => $originalName,
                    'size' => $attachment->formatted_size,
                    'url' => $attachment->url,
                    'thumbnail_url' => $attachment->thumbnail_url,
                    'icon' => $attachment->icon_class,
                    'is_image' => $attachment->isImage(),
                    'is_document' => $attachment->isDocument(),
                    'is_archive' => $attachment->isArchive(),
                    'is_primary' => $attachment->is_primary,
                    'description' => $attachment->description,
                ]
            ]);
        }

        return response()->json(['error' => 'Aucun fichier fourni'], 400);
    }

    /**
     * Supprimer un fichier joint
     */
    public function removeAttachment(KnowledgeBase $article, KnowledgeBaseAttachment $attachment)
    {
        // Vérifier les permissions
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le fichier appartient bien à l'article
        if ($attachment->article_id !== $article->id) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }

        // Supprimer le fichier
        Storage::delete($attachment->chemin);
        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fichier supprimé avec succès',
        ]);
    }

    /**
     * Définir un fichier comme primaire
     */
    public function setPrimary(KnowledgeBase $article, KnowledgeBaseAttachment $attachment)
    {
        // Vérifier les permissions
        if (!in_array(Auth::user()->role->titre, ['Administrateur', 'Superviseur'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le fichier appartient bien à l'article
        if ($attachment->article_id !== $article->id) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }

        // Définir comme primaire
        $attachment->makePrimary();

        return response()->json([
            'success' => true,
            'message' => 'Fichier défini comme primaire avec succès',
        ]);
    }
}
