<?php
namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use App\Models\Taskdocument; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TacheController extends Controller
{
    public function index(Projet $projet)
    {
        $taches = $projet->taches()
            ->with(['users.fonction', 'users.role', 'taskdocuments']) 
            ->when(Schema::hasColumn('taches', 'ordre'), function($query) {
                $query->orderBy('ordre');
            }, function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->get();

        return view('chef_equipe.projets.taches.index', [
            'projet' => $projet,
            'taches' => $taches,
            'priorites' => \App\Models\Tache::PRIORITES,
            'statuts' => \App\Models\Tache::STATUTS
        ]);
    }


    public function create(Projet $projet)
    {
        $users = $projet->equipes()
            ->with('utilisateurs.fonction', 'utilisateurs.role')
            ->get()
            ->flatMap->utilisateurs
            ->unique('id');

        return view('chef_equipe.projets.taches.create', [
            'projet' => $projet,
            'users' => $users,
            'priorites' => Tache::PRIORITES,
            'statuts' => Tache::STATUTS,
            'tache' => null, 
        ]);
    }


    public function store(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'priorite' => 'required|in:basse,moyenne,haute',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'documents.*' => 'file|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg|max:2048'
        ]);

        $tache = new Tache($validated);
        $tache->projet_id = $projet->id;
        $tache->created_by = Auth::id();
        $tache->statut = 'a_faire';
        $tache->ordre = $projet->taches()->max('ordre') + 1;
        $tache->save();

       
        $tache->users()->sync($validated['users']);

        // upload documents 
        if ($request->hasFile('taskdocuments')) {
            foreach ($request->file('taskdocuments') as $file) {
                $path = $file->store('documents/taches/' . $tache->id, 'public');
                Taskdocument::create([
                    'tache_id' => $tache->id,
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'uploaded_by' => Auth::id(),
                    'parent_id' => null,
                    'version' => 1,
                ]);
            }
        }

        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit(Projet $projet, Tache $tache)
    {
        $users = $projet->equipes()
            ->with('utilisateurs.fonction', 'utilisateurs.role')
            ->get()
            ->flatMap->utilisateurs
            ->unique('id');

        return view('chef_equipe.projets.taches.edit', [
            'projet' => $projet,
            'tache' => $tache->load('users', 'taskdocuments'),
            'users' => $users,
            'priorites' => Tache::PRIORITES,
            'statuts' => Tache::STATUTS,
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
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'new_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
        ]);

        $tache->update($validated);

        // update users
        $tache->users()->sync($validated['users']);

        // upload docs
        if ($request->hasFile('new_documents')) {
            foreach ($request->file('new_documents') as $file) {
                $path = $file->store('documents/taches/' . $tache->id, 'public');
                Taskdocument::create([
                    'tache_id' => $tache->id,
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'uploaded_by' => Auth::id(),
                    'parent_id' => null,
                    'version' => 1,
                ]);
            }
        }

        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche mise à jour avec succès.');
    }
    public function uploadDocument(Request $request, Projet $projet, Tache $tache)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'parent_id' => 'nullable|exists:taskdocuments,id'
        ]);

        $file = $request->file('document');
        $path = $file->store('documents/taches/' . $tache->id, 'public');

        $parent = Taskdocument::find($request->parent_id);
        if ($parent) {
            $lastVersion = Taskdocument::where('id', $parent->id)
                            ->orWhere('parent_id', $parent->id)
                            ->max('version');
            $version = $lastVersion + 1; 
        } else {
            $version = 1; 
        }
        $doc = Taskdocument::create([
            'tache_id' => $tache->id,
            'nom_original' => $file->getClientOriginalName(),
            'chemin' => $path,
            'uploaded_by' => Auth::id(),
            'parent_id' => $parent?->id,
            'version' => $version
        ]);
        return redirect()->route('chef_equipe.projets.taches.index', $projet)
                     ->with('success', 'Document ajouté avec succès !');
    }

    public function destroy(Projet $projet, Tache $tache)
    {
        // supprimer les documents physiques
        foreach ($tache->taskdocuments as $doc) {
            Storage::disk('public')->delete($doc->chemin);
            $doc->delete();
        }

        $tache->delete();

        return redirect()->route('chef_equipe.projets.taches.index', $projet)
            ->with('success', 'Tâche supprimée avec succès.');
    }
    public function destroyDocument(Taskdocument $taskdocument)
{
    $tache = $taskdocument->tache;

    // Vérifier que l'utilisateur a le droit de supprimer
    if ($tache->projet->created_by !== auth()->id()) {
        abort(403);
    }

    // Supprimer le fichier physique
    if (Storage::disk('public')->exists($taskdocument->chemin)) {
        Storage::disk('public')->delete($taskdocument->chemin);
    }

    // Supprimer la ligne en base
    $taskdocument->delete();

    return response()->json(['success' => true]);
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