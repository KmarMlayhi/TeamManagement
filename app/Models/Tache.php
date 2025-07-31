<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'date_debut', 'date_fin_prevue', 'date_fin_reelle',
        'priorite', 'statut', 'projet_id', 'affecte_a'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function affecteA()
    {
        return $this->belongsTo(User::class, 'affecte_a');
    }
}