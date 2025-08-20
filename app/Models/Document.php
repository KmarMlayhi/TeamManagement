<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id', 'nom', 'chemin', 'type', 'taille'
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/'.$this->chemin);
    }
}