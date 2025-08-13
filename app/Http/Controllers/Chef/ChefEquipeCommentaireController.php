<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commentaire;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class ChefEquipeCommentaireController extends Controller
{
    public function index(Request $request)
    {
        $chef = Auth::user();

        if ($chef->role->name !== 'chef_equipe') {
            abort(403, 'Non autorisé');
        }

        // Récupérer tous les projets avec commentaires (pour le select)
        $projets = Projet::whereHas('commentaires', function($query) use ($chef) {
            $query->where('destinataire_id', $chef->id);
        })->get();

        // Si un projet est sélectionné
        $selectedProjet = null;
        $commentaires = collect();
        
        if ($request->has('projet_id')) {
            $selectedProjet = Projet::findOrFail($request->projet_id);
            
            $commentaires = Commentaire::with(['auteur:id,name', 'tache:id,titre', 'reponses.auteur:id,name'])
                ->where('destinataire_id', $chef->id)
                ->where('projet_id', $request->projet_id)
                ->orderBy('created_at', 'desc')
                ->paginate(6)
                ->withQueryString();
        }

       

        return view('chef_equipe.commentaires.index', compact('projets', 'selectedProjet', 'commentaires'));
    }

    public function repondre(Request $request, Commentaire $commentaire)
{
    $chef = Auth::user();

    if ($chef->role->name !== 'chef_equipe') {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    $request->validate([
        'contenu' => 'required|string|max:1000',
    ]);

    $reponse = Commentaire::create([
        'projet_id' => $commentaire->projet_id,
        'tache_id' => $commentaire->tache_id,
        'auteur_id' => $chef->id,
        'destinataire_id' => $commentaire->auteur_id,
        'contenu' => $request->contenu,
        'parent_id' => $commentaire->id, // <-- important
    ]);

    $reponse->load('auteur:id,name');

    return response()->json([
        'success' => true,
        'reponse' => [
            'id' => $reponse->id,
            'auteur' => $reponse->auteur->name,
            'contenu' => $reponse->contenu,
            'created_at_humans' => $reponse->created_at->diffForHumans(),
        ]
    ]);
}

    public function destroy(Commentaire $commentaire)
    {
        $chef = Auth::user();

        if ($chef->role->name !== 'chef_equipe' && $chef->id !== $commentaire->auteur_id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $commentaire->delete();

        return response()->json(['success' => true]);
    }
}