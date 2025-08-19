<?php

namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Tache;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class CollaborateurController extends Controller
{
    public function home()
    {
        return view('collaborateur.home');
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
}
