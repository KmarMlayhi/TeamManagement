<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

      protected $fillable = [
        'titre', 'description', 'date_debut', 'date_fin_prevue', 
        'priorite', 'statut', 'projet_id', 'affecte_a', 'ordre' , 'created_by'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
    ];
    
    // Statuts possibles pour le Kanban
    const STATUTS = [
        'a_faire' => 'À faire',
        'en_cours' => 'En cours',
        'termine' => 'Terminé'
    ];

    // Priorités
    const PRIORITES = [
        'basse' => 'Basse',
        'moyenne' => 'Moyenne', 
        'haute' => 'Haute'
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function affecteA()
    {
        return $this->belongsTo(User::class, 'affecte_a');
    }
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
}