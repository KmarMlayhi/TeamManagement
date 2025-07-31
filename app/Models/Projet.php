<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'description', 'date_debut', 'date_fin_prevue', 'date_fin_reelle',
        'statut', 'client', 'details_importants', 'cahier_charge_path', 'equipe_id',
        'created_by', 'budget', 'avancement'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
    ];

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    public function equipes()
    {
        return $this->belongsToMany(Equipe::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Dans Projet.php
    public function getStatutTextAttribute()
    {
        return [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
        ][$this->statut];
    }

    public function getStatutColorAttribute()
    {
        return [
            'en_attente' => 'secondary',
            'en_cours' => 'primary',
            'termine' => 'success',
            'suspendu' => 'warning',
        ][$this->statut];
    }
}