<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Equipe;
use App\Models\Tache;
use App\Models\Projet;

class ChefEquipeController extends Controller
{
    public function dashboard()
    {
        $chefId = Auth::id();
        
        // Statistiques à récupérer
        $stats = [
            'equipes_count' => Equipe::where('created_by', $chefId)->count(),
            'membres_count' => $this->getTotalMembers($chefId),
            'taches_en_cours' => $this->getTasksCount($chefId),
            'projets_actifs' => $this->getActiveProjects($chefId)
        ];

        return view('chef_equipe.dashboard', compact('stats'));
    }

    private function getTotalMembers($chefId)
    {
        // Récupérer toutes les équipes créées par le chef
        $equipesIds = Equipe::where('created_by', $chefId)->pluck('id');
        
        // Compter les membres uniques dans toutes ces équipes
        return \DB::table('equipe_utilisateur')
            ->whereIn('equipe_id', $equipesIds)
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getTasksCount($chefId)
    {
        // Tâches en cours dans les projets gérés par le chef
        return Tache::whereHas('projet.equipes', function($query) use ($chefId) {
            $query->where('created_by', $chefId);
        })
        ->where('statut', 'en_cours')
        ->count();
    }

    private function getActiveProjects($chefId)
    {
        // Projets actifs (en cours) gérés par le chef
        return Projet::whereHas('equipes', function($query) use ($chefId) {
            $query->where('created_by', $chefId);
        })
        ->where('statut', 'en_cours')
        ->count();
    }

    public function suivi()
    {
        $userId = Auth::id();

        // Récupérer tous les projets du chef d'équipe
        $projets = Projet::whereHas('equipes.utilisateurs', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })->get();

        return view('chef_equipe.suivi', compact('projets'));
    }

    public function kanbanProjet(Projet $projet)
    {
        $userId = Auth::id();

        // Vérifier que l'utilisateur est bien membre du projet
        $estMembre = $projet->equipes()->whereHas('utilisateurs', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })->exists();

        if (!$estMembre) {
            abort(403, 'Accès non autorisé');
        }

        // Charger les tâches par statut
        $tachesCollection = Tache::where('projet_id', $projet->id)
            ->with('affecteA')
            ->get()
            ->groupBy('statut');

        $statuts = Tache::STATUTS;
        $priorites = Tache::PRIORITES;

        return view('chef_equipe.kanban', [
            'projet' => $projet,
            'taches' => $tachesCollection,
            'statuts' => $statuts,
            'priorites' => $priorites,
        ]);
    }

}