<?php

namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Projet;
use App\Models\ProjectMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function projetsDiscussion()
    {
        $user = Auth::user();

        // Récupérer les projets 
        $projets = Projet::whereHas('equipes', function ($q) use ($user) {
            $q->whereIn('equipes.id', $user->equipes->pluck('id'));
        })->get();

        return view('collaborateur.projet_messages.projet_list', compact('projets'));
    }

// Afficher les messages d'un projet
    public function showProjetMessages(Projet $projet)
    {
        $messages = ProjectMessage::with('user.role')
                          ->where('projet_id', $projet->id)
                          ->orderBy('created_at', 'asc')
                          ->get();

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

        return view('collaborateur.projet_messages.index', compact('projet', 'messages'));
    }

// Ajouter un message

    public function storeMessage(Request $request, Projet $projet)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'fichier' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx|max:20480' // 20 Mo
        ]);

        $user = Auth::user();

        // Vérifie que l'utilisateur appartient bien au projet
        $userIds = $projet->equipes()->with('utilisateurs')->get()
                        ->pluck('utilisateurs.*.id')
                        ->flatten()
                        ->unique();

        if (!$userIds->contains($user->id)) {
            abort(403, "Vous n'êtes pas affecté à ce projet");
        }

        // Gestion de l'upload du fichier 
        $fichierPath = null;
        $fichierOriginal = null;
        if ($request->hasFile('fichier')) {
            // Stocke dans storage/app/public/messages_fichiers
            $fichierPath = $request->file('fichier')->store('messages_fichiers', 'public');
            $fichierOriginal = $request->file('fichier')->getClientOriginalName();
        }

        // Empêcher l'envoi d'un message complètement vide
        if (empty($request->message) && !$fichierPath) {
            return back()->withErrors(['message' => 'Vous devez écrire un message ou envoyer un fichier.']);
        }

        // Sauvegarde du message
        ProjectMessage::create([
            'projet_id'        => $projet->id,
            'user_id'          => $user->id,
            'message'          => $request->message,
            'fichier'          => $fichierPath,
            'fichier_original' => $fichierOriginal
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

            $users = $equipes->pluck('utilisateurs')
                            ->flatten()
                            ->unique('id')
                            ->values();

            // Ajouter un attribut pour indiquer si c'est un chef d'équipe
            $users->transform(function ($user) {
                $user->is_chef = $user->role && $user->role->name === 'chef_equipe';
                return $user;
            });

            $usersArray = $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_chef' => $user->is_chef
                ];
            });

            return response()->json($usersArray);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
