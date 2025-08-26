<?php
namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commentaire;
use App\Models\Tache;
use Illuminate\Support\Facades\Auth;

class TacheCommentaireController extends Controller
{
    // Afficher les commentaires
    public function index(Tache $tache)
    {
        $user = Auth::user();

        
        if ($user->id !== $tache->created_by && !$tache->users->contains($user)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $commentaires = $tache->commentaires()
            ->with(['auteur:id,name', 'destinataire:id,name'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'commentaires' => $commentaires,
            'canEdit' => $user->id === $tache->created_by
        ]);
    }

    // Créer un commentaire
    public function store(Request $request, Tache $tache)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        if ($user->id !== $tache->created_by && !$tache->users->contains($user)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $commentaire = $tache->commentaires()->create([
            'projet_id' => $tache->projet_id,
            'auteur_id' => $user->id,
            'destinataire_id' => $tache->created_by,
            'contenu' => $request->contenu,
        ]);

        return $commentaire->load(['auteur:id,name', 'destinataire:id,name']);
    }

    // Mettre à jour un commentaire
    public function update(Request $request, $tacheId, $commentaireId)
    {
        $user = Auth::user();

        $commentaire = Commentaire::where('id', $commentaireId)
            ->where('tache_id', $tacheId)
            ->firstOrFail();

        // Vérification : seul l'auteur du commentaire peut le modifier
        if ($user->id !== $commentaire->auteur_id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        $commentaire->update([
            'contenu' => $request->contenu,
            'edited_at' => now()
        ]);

        return $commentaire->load(['auteur:id,name', 'destinataire:id,name']);
    }

    // Supprimer un commentaire
    public function destroy($tacheId, $commentaireId)
    {
        $user = Auth::user();

        $commentaire = Commentaire::where('id', $commentaireId)
            ->where('tache_id', $tacheId)
            ->firstOrFail();

        // Vérification : auteur du commentaire ou créateur de la tâche
        if ($user->id !== $commentaire->auteur_id && !$commentaire->tache->users->contains($user) && $user->id !== $commentaire->tache->created_by) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $commentaire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé'
        ]);
    }
}
