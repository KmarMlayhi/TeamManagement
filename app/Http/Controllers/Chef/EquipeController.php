<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EquipeController extends Controller
{
    public function index()
    {
        $query = Equipe::with(['utilisateurs', 'children', 'parent'])
                 ->latest();
        
        if (Schema::hasColumn('equipes', 'created_by')) {
            $query->where('created_by', Auth::id());
        }

        $equipes = $query->orderBy('niveau')
                        ->orderBy('nom')
                        ->paginate(5);

        return view('chef_equipe.equipes.index', compact('equipes'));
    }

    public function create()
    {
        $users = User::where('is_validated', true)->get();
        $query = Equipe::where('niveau', '<', 4);
        

        $equipes = $query->get();
        
        return view('chef_equipe.equipes.create', compact('users', 'equipes'));
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255|unique:equipes,nom',
        'parent_id' => [
            'nullable',
            'exists:equipes,id',
            function ($attribute, $value, $fail) {
                if ($value) {
                    $parent = Equipe::find($value);
                    if ($parent && $parent->niveau >= 4) {
                        $fail('La hiérarchie ne peut pas dépasser 5 niveaux.');
                    }
                }
            }
        ],
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id'
    ]);

    // TOUJOURS ajouter created_by si l'utilisateur est connecté
    $validated['created_by'] = Auth::id();

    $equipe = Equipe::create($validated);
    $equipe->utilisateurs()->sync($validated['user_ids']);

    return redirect()->route('chef_equipe.equipes.index')
           ->with('success', 'Équipe créée avec succès.');
}

    public function edit($id)
    {
        $query = Equipe::with('utilisateurs');

        $equipe = $query->findOrFail($id);
        $users = User::where('is_validated', true)->get();
        
        $equipesQuery = Equipe::where('id', '!=', $id)
                            ->where('niveau', '<', 4);

        $autresEquipes = $equipesQuery->get();

        return view('chef_equipe.equipes.edit', compact('equipe', 'users', 'autresEquipes'));
    }

    public function update(Request $request, $id)
    {
        $query = Equipe::query();

        $equipe = $query->findOrFail($id);

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('equipes')->ignore($equipe->id)
            ],
            'parent_id' => [
                'nullable',
                'exists:equipes,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Une équipe ne peut pas être son propre parent.');
                    }
                    
                    $current = Equipe::find($value);
                    while ($current) {
                        if ($current->parent_id == $id) {
                            $fail('Cette sélection créerait une référence circulaire.');
                        }
                        $current = $current->parent;
                    }
                }
            ],
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $equipe->update($validated);
        $equipe->utilisateurs()->sync($validated['user_ids']);

        return redirect()->route('chef_equipe.equipes.index')
               ->with('success', 'Équipe mise à jour avec succès.');
    }

    
    public function destroy($id)
{
    $equipe = Equipe::findOrFail($id);

    if ($equipe->children()->exists()) {
        return back()->withErrors(['message' => 'Impossible de supprimer : sous-équipes existantes']);
    }

    if ($equipe->projets()->exists()) {
        return back()->withErrors(['message' => 'Impossible de supprimer : associée à des projets']);
    }

    $equipe->utilisateurs()->detach();
    $equipe->delete();

    return redirect()->route('chef_equipe.equipes.index')->with('success', 'Équipe supprimée');
}
    public function details(Equipe $equipe)
{
    return response()->json([
        'nom' => $equipe->nom,
        'niveau' => $equipe->niveau,
        'parent' => $equipe->parent ? [
            'nom' => $equipe->parent->nom,
            'niveau' => $equipe->parent->niveau
        ] : null,
        'membres' => $equipe->utilisateurs->map(function($user) {
            return [
                'name' => $user->name,
                'fonction' => $user->fonction,
                'role' => $user->role,
                'email' => $user->email
            ];
        }),
         'sous_equipes' => $equipe->children()->withCount('utilisateurs')->get()->map(function($child) {
        return [
            'nom' => $child->nom,
            'niveau' => $child->niveau, // Ajout du niveau
            'membres_count' => $child->utilisateurs_count
        ];
    })
    ]);
}
}