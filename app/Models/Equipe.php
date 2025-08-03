<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'parent_id', 'niveau','created_by']; 
    protected static function booted()
    {
        static::creating(function ($equipe) {
            self::updateNiveau($equipe);
        });

        static::updating(function ($equipe) {
            if ($equipe->isDirty('parent_id')) {
                self::updateNiveau($equipe);
                $equipe->updateChildrenNiveau();
            }
        });
    }
    protected static function updateNiveau($equipe)
    {
        $equipe->niveau = $equipe->parent_id 
            ? $equipe->parent->niveau + 1 
            : 1;
    }

    public function updateChildrenNiveau()
    {
        foreach ($this->children as $child) {
            $child->niveau = $this->niveau + 1;
            $child->save();
            $child->updateChildrenNiveau(); // Récursivité
        }
    }

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
        return $this->belongsToMany(Projet::class, 'projet_equipe');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
     public function getNiveauCompletAttribute()
    {
        return "Niveau {$this->niveau}";
    }
}
