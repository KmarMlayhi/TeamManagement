<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taskdocument extends Model
{
    protected $fillable = ['nom_original', 'chemin', 'tache_id','uploaded_by', 'parent_id', 'version'];

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Le document parent (null si c'est la première version)
    public function parent()
    {
        return $this->belongsTo(Taskdocument::class, 'parent_id');
    }

    // Toutes les versions enfants de ce document
    public function versions()
    {
        return $this->hasMany(Taskdocument::class, 'parent_id');
    }
    
}
