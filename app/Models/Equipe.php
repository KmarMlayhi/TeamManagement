<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'parent_id'];

    public function utilisateurs()
    {
        return $this->belongsToMany(User::class, 'equipe_utilisateur');
    }

    public function parent()
    {
        return $this->belongsTo(Equipe::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Equipe::class, 'parent_id');
    }
    public function projets()
    {
        return $this->belongsToMany(Projet::class);
    }
}
