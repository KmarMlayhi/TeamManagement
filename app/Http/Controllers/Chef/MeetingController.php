<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Notifications\ReunionCreee;

class MeetingController extends Controller
{
    public function createMeeting($projetId)
    {
        // Récupérer le projet
        $projet = Projet::findOrFail($projetId);

        // Générer un identifiant unique pour la réunion 
        $meetingID = 'projet_' . $projetId . '_' . uniqid();

        // Lien de réunion Jitsi
        $joinUrl = "https://meet.jit.si/" . $meetingID;

        // Envoyer la notification aux collaborateurs
        $collaborateurs = $projet->collaborateurs(); 

        foreach ($collaborateurs as $collab) {
            $collab->notify(new ReunionCreee($projet, $joinUrl));
        }

        // Rediriger directement l'utilisateur vers la réunion
        return redirect($joinUrl);
    }
}
