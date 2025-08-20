<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReunionCreee extends Notification
{
    use Queueable;

    protected $projet;
    protected $lien;

    // Recevoir le projet et le lien de la réunion
    public function __construct($projet, $lien)
    {
        $this->projet = $projet;
        $this->lien = $lien;
    }

    // Canaux : base de données seulement pour l'instant
    public function via($notifiable)
    {
        return ['database'];
    }

    // Contenu stocké dans la table notifications
    public function toDatabase($notifiable)
    {
        return [
            'projet_id' => $this->projet->id,
            'projet_nom' => $this->projet->nom,
            'lien' => $this->lien,
            'message' => "Une nouvelle réunion a été créée pour le projet {$this->projet->nom}"
        ];
    }
}
