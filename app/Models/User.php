<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Les attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role_id',
    'is_validated',
    'avatar',
    'poste',
    'grade_id',      // clé étrangère vers table grades
    'fonction_id',   // clé étrangère vers table fonctions
    'direction_id',  // clé étrangère vers table directions
    ];


    /**
     * Les attributs à cacher pour les tableaux
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à caster automatiquement.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_validated' => 'boolean',
    ];
    
    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'equipe_utilisateur');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function commentairesEnvoyes()
    {
        return $this->hasMany(Commentaire::class, 'auteur_id');
    }

    public function commentairesRecus()
    {
        return $this->hasMany(Commentaire::class, 'destinataire_id');
    }
    public function taches()
    {
        return $this->belongsToMany(\App\Models\Tache::class, 'tache_user')->withTimestamps();
    }

}
