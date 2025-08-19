<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Projet;
use App\Models\ProjectMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Afficher la liste des projets pour discussion
    // Dans ChatController.php

    public function projetsDiscussion()
    {
        $user = Auth::user();

        $projets = Projet::whereHas('equipes', function ($q) use ($user) {
            $q->whereIn('equipes.id', $user->equipes->pluck('id'));
        })->get();

        return view('chef_equipe.project_messages.projet_list', compact('projets'));
    }



    // Afficher les messages d'un projet
    public function showProjetMessages(Projet $projet)
    {
        $user = Auth::user();

        $userIds = $projet->equipes()->with('utilisateurs')->get()
                           ->pluck('utilisateurs.*.id')
                           ->flatten()
                           ->unique();

        if (!$userIds->contains($user->id)) {
            abort(403, "Vous n'êtes pas affecté à ce projet");
        }

        $messages = ProjectMessage::with('user')
                                  ->where('projet_id', $projet->id)
                                  ->orderBy('created_at', 'asc')
                                  ->get();

        return view('chef_equipe.project_messages.index', compact('projet', 'messages'));

    }

    // Ajouter un message
    public function storeMessage(Request $request, Projet $projet)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();

        $userIds = $projet->equipes()->with('utilisateurs')->get()
                           ->pluck('utilisateurs.*.id')
                           ->flatten()
                           ->unique();

        if (!$userIds->contains($user->id)) {
            abort(403, "Vous n'êtes pas affecté à ce projet");
        }

        ProjectMessage::create([
            'projet_id' => $projet->id,
            'user_id' => $user->id,
            'message' => $request->message
        ]);

        return redirect()->back();
    }
    public function projectUsers(Projet $projet)
{
    try {
        $user = Auth::user();

        // Vérifie que l'utilisateur a accès au projet
        $userEquipeIds = $user->equipes->pluck('id')->toArray();
        $projetEquipeIds = $projet->equipes->pluck('id')->toArray();
        if (count(array_intersect($userEquipeIds, $projetEquipeIds)) === 0) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Récupérer toutes les équipes du projet avec leurs utilisateurs et rôles
        $equipes = $projet->equipes()->with('utilisateurs.role')->get();

        // Extraire tous les utilisateurs uniques
        $users = $equipes->pluck('utilisateurs')
                         ->flatten()
                         ->unique('id')
                         ->values();

        // Ajouter un attribut pour indiquer si c'est un chef d'équipe
        $users->transform(function ($user) {
            $user->is_chef = $user->role && $user->role->name === 'chef_equipe';
            return $user;
        });

        // Retourner seulement les champs utiles
        $usersArray = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'is_chef' => $user->is_chef
            ];
        });

        return response()->json($usersArray);

    } catch (\Exception $e) {
        // Retourner l'erreur pour debug
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
