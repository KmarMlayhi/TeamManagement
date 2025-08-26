<?php

namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Taskdocument;
use App\Models\Tache;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class CollaborateurController extends Controller
{

    public function home()
    {
        $user = Auth::user();

        $mesProjets = $user->equipes()
            ->with('projets')
            ->get()
            ->pluck('projets')
            ->flatten()
            ->unique('id');

        $taches = $user->relationLoaded('taches') ? $user->taches : $user->taches()->get();
        $tachesEnCours = $taches ? $taches->where('statut', 'en_cours')->count() : 0;
        $tachesTerminees = $taches ? $taches->where('statut', 'termine')->count() : 0;
        $tachesEnRetard = $taches ? $taches->filter(function($tache) {
            return $tache->date_fin_prevue && $tache->date_fin_prevue < now() && $tache->statut != 'termine';
        })->count() : 0;

        $projetsRecents = $mesProjets ? $mesProjets->sortByDesc('created_at')->take(3) : collect();

        // --- Charger les notifications non lues ---
        $notifications = $user->unreadNotifications;

        return view('collaborateur.home', compact(
            'mesProjets', 
            'tachesEnCours', 
            'tachesTerminees', 
            'tachesEnRetard', 
            'projetsRecents',
            'notifications'
        ));

    }
    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['lien']);
    }


    public function equipesIndex()
    {
        $user = Auth::user()->load('equipes');
        return view('collaborateur.equipes.index', ['equipes' => $user->equipes]);
    }

    public function equipesShow(Equipe $equipe)
    {
        if (!Auth::user()->equipes->contains($equipe)) {
            abort(403, "Vous n'avez pas accès à cette équipe");
        }

        $equipe->load([
            'utilisateurs.fonction',
            'utilisateurs.role',
            'projets',
            'parent',
            'children'
        ]);

        return view('collaborateur.equipes.show', compact('equipe'));
    }

    public function projetDetails(Projet $projet)
    {
        $user = Auth::user();
        $userEquipesIds = $user->equipes->pluck('id')->toArray();
        $projetEquipesIds = $projet->equipes->pluck('id')->toArray();

        if (count(array_intersect($userEquipesIds, $projetEquipesIds)) === 0) {
            abort(403, "Vous n'avez pas accès à ce projet");
        }

        $projet->load([
            'equipes.utilisateurs.fonction',
            'equipes.utilisateurs.role',
            'documents',
            'createdBy'
        ]);

        $date_debut_formatted = $projet->date_debut->format('d/m/Y');
        $date_fin_prevue_formatted = $projet->date_fin_prevue->format('d/m/Y');
        $date_fin_reelle_formatted = $projet->date_fin_reelle ? $projet->date_fin_reelle->format('d/m/Y') : null;

        $documents = $projet->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'nom' => $document->nom,
                'type' => $document->type,
                'url' => asset('storage/' . $document->chemin),
                'size' => $this->formatFileSize($document->taille)
            ];
        });

        $equipes = $projet->equipes->map(function ($equipe) {
            return [
                'id' => $equipe->id,
                'nom' => $equipe->nom,
                'utilisateurs' => $equipe->utilisateurs->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'fonction' => $user->fonction ? $user->fonction->nom : 'Non spécifiée',
                        'email' => $user->email,
                        'role' => $user->role?->name ?? '',
                    ];
                })
            ];
        });

        return response()->json([
            'id' => $projet->id,
            'nom' => $projet->nom,
            'description' => $projet->description,
            'client' => $projet->client,
            'date_debut_formatted' => $date_debut_formatted,
            'date_fin_prevue_formatted' => $date_fin_prevue_formatted,
            'date_fin_reelle_formatted' => $date_fin_reelle_formatted,
            'budget' => $projet->budget,
            'statut' => $projet->statut,
            'statut_text' => $projet->statut_text,
            'statut_class' => $projet->statut_color,
            'progression' => $projet->avancement,
            'created_by' => $projet->createdBy->name ?? 'Inconnu',
            'documents' => $documents,
            'equipes' => $equipes
        ]);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        if ($bytes > 1) return $bytes . ' bytes';
        if ($bytes == 1) return '1 byte';
        return '0 bytes';
    }

    // Liste des tâches pour un projet et une équipe
    public function projetTaches(Projet $projet)
    {
        $user = Auth::user();
        $equipeId = request('equipe_id');

        $equipe = $projet->equipes()
            ->whereIn('id', $user->equipes->pluck('id'))
            ->where('id', $equipeId)
            ->first();

        if (!$equipe) abort(403, "Vous n'avez pas accès à cette équipe");

        $taches = Tache::where('projet_id', $projet->id)
            ->where(function($query) use ($user) {
                $query->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                      ->orWhere('created_by', $user->id);
            })
            ->with(['projet', 'users'])
            ->orderBy('date_fin_prevue', 'asc')
            ->get();

        return view('collaborateur.taches.index', compact('taches', 'projet', 'equipe'));
    }

    public function projetTachesKanban(Projet $projet)
    {
        $user = Auth::user();
        $equipeId = request('equipe_id');

        $equipe = $projet->equipes()
            ->whereIn('id', $user->equipes->pluck('id'))
            ->where('id', $equipeId)
            ->first();

        if (!$equipe) abort(403, "Vous n'avez pas accès à cette équipe");

        $taches = Tache::where('projet_id', $projet->id)
            ->where(function($query) use ($user) {
                $query->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                      ->orWhere('created_by', $user->id);
            })
            ->with(['projet', 'users'])
            ->get();

        $statuts = Tache::STATUTS;
        $priorites = Tache::PRIORITES;

        $tachesGrouped = [];
        foreach ($statuts as $key => $label) {
            $tachesGrouped[$key] = $taches->where('statut', $key)->all();
        }

        return view('collaborateur.taches.kanban', compact('projet', 'equipe', 'tachesGrouped', 'statuts', 'priorites'));
    }

    // Afficher une tâche
    public function showTache(Tache $tache)
    {
        $user = Auth::user();

        if (!$tache->users->contains($user) && $tache->created_by !== $user->id) {
            abort(403, "Vous n'êtes pas assigné à cette tâche");
        }

        $equipe = $tache->projet->equipes()
            ->whereIn('id', $user->equipes->pluck('id'))
            ->first();

        if (!$equipe) abort(403, "Vous n'avez pas accès à ce projet");

        $tache->load('taskdocuments');

        return view('collaborateur.taches.show', compact('tache', 'equipe'));
    }

    // Mise à jour du statut
    public function updateStatut(Request $request, Tache $tache)
    {
        $user = Auth::user();
        if (!$tache->users->contains($user) && $tache->created_by !== $user->id) {
            abort(403, "Vous n'êtes pas assigné à cette tâche");
        }

        $request->validate([
            'statut' => 'required|in:a_faire,en_cours,termine'
        ]);

        $tache->update([
            'statut' => $request->statut,
            'date_fin_reelle' => $request->statut === 'termine' ? now() : null
        ]);

        return redirect()->back()->with('success', 'Statut de la tâche mis à jour avec succès');
    }

    // Mise à jour du statut via Kanban
    public function updateStatutKanban(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'statut' => 'required|in:a_faire,en_cours,termine'
        ]);

        $tache = Tache::findOrFail($request->tache_id);
        $user = Auth::user();

        if (!$tache->users->contains($user) && $tache->created_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tache->update([
            'statut' => $request->statut,
            'date_fin_reelle' => $request->statut === 'termine' ? now() : null
        ]);

        return response()->json(['success' => true]);
    }

    // Vue Kanban globale
    public function kanbanProjet(Projet $projet)
    {
        $userId = Auth::id();
        $estMembre = $projet->equipes()->whereHas('utilisateurs', fn($q) => $q->where('users.id', $userId))->exists();

        if (!$estMembre) abort(403, 'Accès non autorisé');

        $tachesCollection = Tache::where('projet_id', $projet->id)
            ->where(function($query) use ($userId) {
                $query->whereHas('users', fn($q) => $q->where('users.id', $userId))
                      ->orWhere('created_by', $userId);
            })
            ->with('users')
            ->get()
            ->groupBy('statut');

        $statuts = Tache::STATUTS;
        $priorites = Tache::PRIORITES;

        return view('collaborateur.kanban', compact('projet', 'tachesCollection', 'statuts', 'priorites'));
    }

    public function suivi()
    {
        $userId = Auth::id();
        $projets = Projet::whereHas('equipes.utilisateurs', fn($q) => $q->where('users.id', $userId))->get();
        return view('collaborateur.suivi', compact('projets'));
    }

    public function getDocuments(Tache $tache)
    {
        $documents = Taskdocument::where('tache_id', $tache->id)
            ->whereNull('parent_id')
            ->with([
                'versions' => function($q) {
                    $q->with('uploader')->orderBy('version', 'asc');
                },
                'uploader'
            ])
            ->get();

        $data = $documents->map(function($doc) {
            return [
                'id' => $doc->id,
                'nom_original' => $doc->nom_original,
                'version' => $doc->version, // <-- version réelle de la DB
                'uploader' => $doc->uploader?->name ?? 'Inconnu',
                'uploader_id' => $doc->uploaded_by,
                'created_at' => $doc->created_at->format('d/m/Y H:i'),
                'url' => asset('storage/' . $doc->chemin),
                'versions' => $doc->versions->map(fn($v) => [
                    'id' => $v->id,
                    'nom_original' => $v->nom_original,
                    'version' => $v->version,
                    'uploader' => $v->uploader?->name ?? 'Inconnu',
                    'uploader_id' => $v->uploaded_by,
                    'created_at' => $v->created_at->format('d/m/Y H:i'),
                    'url' => asset('storage/' . $v->chemin),
                ]),
            ];
        });

        return response()->json(['success' => true, 'documents' => $data]);
    }


    public function uploadDocument(Request $request, Tache $tache)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'parent_id' => 'nullable|exists:taskdocuments,id'
        ]);

        $file = $request->file('document');
        $path = $file->store('documents/taches/' . $tache->id, 'public');

        $parent = Taskdocument::find($request->parent_id);

    if ($parent) {
        // Chercher la version du parent et de tous ses enfants
        $lastVersion = Taskdocument::where('id', $parent->id)
                        ->orWhere('parent_id', $parent->id)
                        ->max('version');
        $version = $lastVersion + 1; // Nouvelle version = dernière version + 1
    } else {
        $version = 1; // Premier document
    }

        $doc = Taskdocument::create([
            'tache_id' => $tache->id,
            'nom_original' => $file->getClientOriginalName(),
            'chemin' => $path,
            'uploaded_by' => Auth::id(),
            'parent_id' => $parent?->id,
            'version' => $version
        ]);

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $doc->id,
                'nom_original' => $doc->nom_original,
                'version' => $doc->version,
                'uploader' => $doc->uploader?->name ?? 'Inconnu',
                'created_at' => $doc->created_at->format('d/m/Y H:i'),
                'url' => asset('storage/' . $doc->chemin),
                'parent_id' => $doc->parent_id
            ]
        ]);
    }

    public function deleteDocument(Tache $tache, Taskdocument $document)
    {
        if ($document->uploaded_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer ce document'], 403);
        }

        $document->delete();
        return response()->json(['success' => true]);
    }

}
