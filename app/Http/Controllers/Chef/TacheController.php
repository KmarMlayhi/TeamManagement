<?php
namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class TacheController extends Controller
{
    public function index(Projet $projet)
{
    $taches = $projet->taches()
        ->with('affecteA');
    
    // Vérifiez si la colonne existe avant de trier
    if (Schema::hasColumn('taches', 'ordre')) {
        $taches->orderBy('ordre');
    } else {
        $taches->orderBy('created_at', 'desc');
    }

    $taches = $taches->get();

    return view('chef_equipe.projets.taches.index',[ 'projet' => $projet,
        'taches' => $taches,
        'priorites' => \App\Models\Tache::PRIORITES,
        'statuts' => \App\Models\Tache::STATUTS]);
}

    public function create(Projet $projet)
    {
        $users = $projet->equipes->flatMap->utilisateurs->unique();
        return view('chef_equipe.projets.taches.create',
        ['projet' => $projet,
        'users' => $users,
        'priorites' => Tache::PRIORITES, 
        'statuts' => Tache::STATUTS,]);
    }

    public function store(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'priorite' => 'required|in:basse,moyenne,haute',
            'affecte_a' => 'required|exists:users,id',
        ]);

        $tache = new Tache($validated);
        $tache->projet_id = $projet->id;
        $tache->created_by = Auth::id();
        $tache->statut = 'a_faire';
        $tache->ordre = $projet->taches()->max('ordre') + 1;
        $tache->save();

        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit(Projet $projet, Tache $tache)
    {
        $users = $projet->equipes->flatMap->utilisateurs->unique();
        return view('chef_equipe.projets.taches.edit', [
        'projet' => $projet,
        'tache' => $tache,
        'users' => $users,
        'priorites' => \App\Models\Tache::PRIORITES,
        'statuts' => \App\Models\Tache::STATUTS
    ]);
    }

    public function update(Request $request, Projet $projet, Tache $tache)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'priorite' => 'required|in:basse,moyenne,haute',
            'statut' => 'required|in:a_faire,en_cours,termine',
            'affecte_a' => 'required|exists:users,id',
        ]);

        $tache->update($validated);

        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    public function destroy(Projet $projet, Tache $tache)
    {
        $tache->delete();
        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche supprimée avec succès.');
    }

    public function reorder(Request $request, Projet $projet)
    {
        $request->validate([
            'taches' => 'required|array',
            'taches.*' => 'exists:taches,id'
        ]);

        foreach ($request->taches as $index => $tacheId) {
            Tache::where('id', $tacheId)
                ->where('projet_id', $projet->id)
                ->update(['ordre' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }



public function kanban(Projet $projet)
{
    // Supprimez ->toArray() pour garder les objets Eloquent
    $tachesGrouped = $projet->taches()
                          ->get()
                          ->groupBy('statut');

    return view('chef_equipe.projets.taches.kanban', [
        'projet' => $projet,
        'taches' => [
            'a_faire' => $tachesGrouped->get('a_faire', collect()),
            'en_cours' => $tachesGrouped->get('en_cours', collect()),
            'termine' => $tachesGrouped->get('termine', collect())
        ],
        'statuts' => Tache::STATUTS,
        'priorites' => Tache::PRIORITES
    ]);
}

    public function updateStatus(Request $request, Projet $projet)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'statut' => 'required|in:a_faire,en_cours,termine'
        ]);

        $tache = Tache::find($request->tache_id);
        $tache->statut = $request->statut;
        $tache->save();

        return response()->json(['success' => true]);
    }
}