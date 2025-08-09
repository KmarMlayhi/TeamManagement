<?php

namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipe;
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
}
