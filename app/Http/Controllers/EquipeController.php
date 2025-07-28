<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    public function index()
    {
       $equipes = Equipe::with(['utilisateurs', 'children', 'parent'])->get();

        return view('admin.equipe.index', compact('equipes'));
    }

    public function create()
    {
        $users = User::all();
        $equipes = Equipe::all();
        return view('admin.equipe.create', compact('users', 'equipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'user_ids' => 'required|array',
        ]);

        $equipe = Equipe::create([
            'nom' => $request->nom,
            'parent_id' => $request->parent_id,
        ]);

        $equipe->utilisateurs()->attach($request->user_ids);

        return redirect()->route('equipes.index')->with('success', 'Équipe créée avec succès.');
    }
    public function edit($id)
{
    $equipe = Equipe::findOrFail($id);
    $utilisateurs = User::all();
    $autresEquipes = Equipe::where('id', '!=', $id)->get();
    return view('admin.equipe.edit', compact('equipe','utilisateurs', 'autresEquipes'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:equipes,id',
        'utilisateurs' => 'nullable|array',
        'utilisateurs.*' => 'exists:users,id',
        'parent_id' => 'nullable|exists:equipes,id',
    ]);

    $equipe = Equipe::findOrFail($id);
    $equipe->nom = $request->nom;
    $equipe->parent_id = $request->parent_id;
    $equipe->save();

    // Synchroniser les membres de l'équipe
    // Cela ajoute les nouveaux, retire les supprimés
    $equipe->utilisateurs()->sync($request->utilisateurs ?? []);

    return redirect()->route('equipes.index')->with('success', 'Équipe mise à jour avec succès.');
}


    public function destroy($id)
    {
        $equipe = Equipe::findOrFail($id);
        $equipe->utilisateurs()->detach(); // détache les relations avant suppression
        $equipe->delete();
        return redirect()->route('equipes.index')->with('success', 'Équipe supprimée avec succès.');
    }
}
