<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'auteur_id',
        'destinataire_id',
        'tache_id',
        'projet_id',
        'contenu',
        'parent_id', // <-- ajouter ici
        'edited_at',
    ];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function reponses()
    {
        return $this->hasMany(Commentaire::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
