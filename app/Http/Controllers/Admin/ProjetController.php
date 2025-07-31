<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $query = Projet::with(['equipe', 'createdBy']);
        
        // Ajout de la recherche par nom
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nom', 'like', "%{$search}%");
        }
        
        // Pagination avec 5 projets par page
        $projets = $query->paginate(5)->withQueryString();
        
        return view('admin.projects.index', compact('projets'));
    }

    public function create()
    {
        $equipes = Equipe::all();
        return view('admin.projects.create', compact('equipes'));
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
            'equipe_id' => 'nullable|exists:equipes,id',
            'budget' => 'nullable|numeric|min:0',
            'cahier_charge' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('cahier_charge')) {
            $path = $request->file('cahier_charge')->store('cahiers_charges');
            $validated['cahier_charge_path'] = $path;
        }

        $validated['created_by'] = auth()->id();
        $validated['avancement'] = 0;
        $validated['description'] = $validated['description'] ?? '';
        $validated['details_importants'] = $validated['details_importants'] ?? '';

        Projet::create($validated);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Projet créé avec succès');
    }
}