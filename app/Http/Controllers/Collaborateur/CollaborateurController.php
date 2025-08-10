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

    // Liste des équipes du collaborateur
    public function equipesIndex()
    {
        // Récupère l'utilisateur connecté avec ses équipes
        $user = Auth::user()->load('equipes');

        return view('collaborateur.equipes.index', [
            'equipes' => $user->equipes
        ]);
    }

    // Détails d'une équipe spécifique
    public function equipesShow(Equipe $equipe)
    {
        // Vérifie que l'utilisateur appartient à l'équipe
        if (!Auth::user()->equipes->contains($equipe)) {
            abort(403, "Vous n'avez pas accès à cette équipe");
        }

        // Charge les relations nécessaires, y compris fonction et rôle des utilisateurs
        $equipe->load([
            'utilisateurs.fonction',
            'utilisateurs.role',
            'projets',
            'parent',
            'children'
        ]);

        return view('collaborateur.equipes.show', compact('equipe'));
    }

    // Détails d'un projet spécifique
    public function projetDetails(Projet $projet)
    {
        // Vérifie que l'utilisateur a accès à ce projet
        $user = Auth::user();
        $userEquipesIds = $user->equipes->pluck('id')->toArray();
        $projetEquipesIds = $projet->equipes->pluck('id')->toArray();

        if (count(array_intersect($userEquipesIds, $projetEquipesIds)) === 0) {
            abort(403, "Vous n'avez pas accès à ce projet");
        }

        // Charge les relations nécessaires
        $projet->load([
            'equipes.utilisateurs.fonction',
            'equipes.utilisateurs.role',
            'documents',
            'createdBy'
        ]);

        // Formater les dates
        $date_debut_formatted = $projet->date_debut->format('d/m/Y');
        $date_fin_prevue_formatted = $projet->date_fin_prevue->format('d/m/Y');
        $date_fin_reelle_formatted = $projet->date_fin_reelle ? $projet->date_fin_reelle->format('d/m/Y') : null;

        // Formater les documents
        $documents = $projet->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'nom' => $document->nom,
                'type' => $document->type,
                'url' => asset('storage/' . $document->chemin),
                'size' => $this->formatFileSize($document->taille)
            ];
        });

        // Formater les équipes impliquées
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

    // Helper pour formater la taille des fichiers
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    
    }

    public function projetTaches(Projet $projet)
{
    $user = Auth::user();
    $equipeId = request('equipe_id');

    // Trouver l'équipe spécifique que l'utilisateur a sélectionnée
    $equipe = $projet->equipes()
        ->whereIn('id', $user->equipes->pluck('id'))
        ->where('id', $equipeId)
        ->first();

    if (!$equipe) {
        abort(403, "Vous n'avez pas accès à cette équipe");
    }

    // Récupérer les tâches POUR CETTE ÉQUIPE SPÉCIFIQUE via la relation utilisateur
    $taches = Tache::where('projet_id', $projet->id)
        ->where('affecte_a', $user->id)
        ->whereHas('affecteA', function ($query) use ($equipeId) {
            $query->whereHas('equipes', function ($q) use ($equipeId) {
                $q->where('equipes.id', $equipeId);
            });
        })
        ->with(['projet', 'affecteA'])
        ->orderBy('date_fin_prevue', 'asc')
        ->get();

    return view('collaborateur.taches.index', compact('taches', 'projet', 'equipe'));
}
    public function showTache(Tache $tache)
    {
        // Vérifier que l'utilisateur est bien assigné à cette tâche
        if ($tache->affecte_a !== Auth::id()) {
            abort(403, "Vous n'êtes pas assigné à cette tâche");
        }

        return view('collaborateur.taches.show', compact('tache'));
    }
}
