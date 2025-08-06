<?php
namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Document;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Projet::with(['equipes', 'createdBy'])
                    ->where('created_by', auth()->id())
                    ->latest();
        
        // Recherche par nom
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nom', 'like', "%{$search}%");
        }
        
        // Pagination avec 5 projets par page
        $projets = $query->paginate(5)->withQueryString();
        
        return view('chef_equipe.projets.index', compact('projets'));
    }

    public function create()
    {
        $equipes = Equipe::all();
        return view('chef_equipe.projets.create', compact('equipes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,termine,suspendu',
            'client' => 'nullable|string|max:255',
            'details_importants' => 'nullable|string',
            'equipe_ids' => 'required|array', // Changer en equipe_ids
            'equipe_ids.*' => 'exists:equipes,id', // Validation pour chaque ID
            'budget' => 'nullable|numeric|min:0',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240', // 10MB max par fichier
        ]);

        $validated['created_by'] = auth()->id();
        $validated['avancement'] = 0;

        // Création du projet
        $projet = Projet::create($validated);
        
        // Synchroniser les équipes (many-to-many)
        $projet->equipes()->sync($validated['equipe_ids']);

        // Traitement des documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('projets/documents', 'public');
                Document::create([
                    'projet_id' => $projet->id,
                    'nom' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'type' => $file->getClientMimeType(),
                    'taille' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('chef_equipe.projets.index')
            ->with('success', 'Projet créé avec succès');
    }

    public function show($id)
    {
        // Modifier 'equipe' en 'equipes'
        $projet = Projet::with(['equipes', 'createdBy', 'documents'])
                      ->where('created_by', auth()->id())
                      ->findOrFail($id);
        
        return view('chef_equipe.projets.show', compact('projet'));
    }

    public function edit($id)
    {
        // Modifier 'equipe' en 'equipes'
        $projet = Projet::with(['documents', 'equipes'])
                       ->where('created_by', auth()->id())
                       ->findOrFail($id);
        
        $equipes = Equipe::all();
        return view('chef_equipe.projets.edit', compact('projet', 'equipes'));
    }

    public function update(Request $request, $id)
    {
        $projet = Projet::where('created_by', auth()->id())
                       ->findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,termine,suspendu',
            'client' => 'nullable|string|max:255',
            'details_importants' => 'nullable|string',
            'equipe_ids' => 'required|array', // Changer en equipe_ids
            'equipe_ids.*' => 'exists:equipes,id', // Validation pour chaque ID
            'budget' => 'nullable|numeric|min:0',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        $projet->update($validated);
        
        // Synchroniser les équipes
        $projet->equipes()->sync($validated['equipe_ids']);

        // Traitement des nouveaux documents
        if ($request->hasFile('new_documents')) {
        foreach ($request->file('new_documents') as $file) {
            if ($file->isValid()) {
                $path = $file->store('projets/documents', 'public');
                
                Document::create([
                    'projet_id' => $projet->id,
                    'nom' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'type' => $file->getClientMimeType(),
                    'taille' => $file->getSize(),
                ]);
            }
        }
    }

    return redirect()->route('chef_equipe.projets.index')
         ->with('success', 'Projet mis à jour avec succès');
}

    public function destroy($id)
    {
        $projet = Projet::with('documents')
                       ->where('created_by', auth()->id())
                       ->findOrFail($id);

        // Suppression des documents associés
        foreach ($projet->documents as $document) {
            Storage::delete($document->chemin);
            $document->delete();
        }

        $projet->delete();

        return redirect()->route('chef_equipe.projets.index')
            ->with('success', 'Projet supprimé avec succès');
    }

    public function deleteDocument($projetId, $documentId)
    {
        $projet = Projet::where('created_by', auth()->id())
                       ->findOrFail($projetId);
        
        $document = $projet->documents()->findOrFail($documentId);
        
        Storage::delete($document->chemin);
        $document->delete();

        return back()->with('success', 'Document supprimé avec succès');
    }


public function details(Projet $projet)
{
    // Charger les relations
    $projet->load([
        'equipes.utilisateurs:id,name,email,fonction', // si tu veux aussi les utilisateurs
        'documents',
    ]);

    return response()->json([
        'id' => $projet->id,
        'nom' => $projet->nom,
        'description' => $projet->description,
        'client' => $projet->client,
        'date_debut_formatted' => $projet->date_debut->format('d/m/Y'),
        'date_fin_prevue_formatted' => $projet->date_fin_prevue->format('d/m/Y'),
        'statut' => $projet->statut,
        'budget' => $projet->budget,
        'statut_text' => $projet->statut_text,
        'statut_class' => $projet->statut === 'termine' ? 'success' : ($projet->statut === 'en_cours' ? 'primary' : 'warning'),
        'progression' => $projet->progression,

        'equipes' => $projet->equipes->map(function ($equipe) {
            return [
                'id' => $equipe->id,
                'nom' => $equipe->nom ?? 'Équipe sans nom',
                'utilisateurs' => $equipe->utilisateurs->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'fonction' => $user->fonction,
                    ];
                }),
            ];
        }),

        'documents' => $projet->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'nom' => $document->nom,
                'type' => pathinfo($document->chemin, PATHINFO_EXTENSION),
                'url' => Storage::url($document->chemin)
            ];
        }),
    ]);
}
public function destroyDocument(Document $document)
{
    // Vérifier que l'utilisateur a le droit de supprimer ce document
    if ($document->projet->created_by !== auth()->id()) {
        abort(403);
    }

    // Supprimer le fichier physique
    Storage::disk('public')->delete($document->chemin);

    // Supprimer l'entrée en base de données
    $document->delete();

    return response()->json(['success' => true]);
}

}
