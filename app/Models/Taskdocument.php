<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taskdocument extends Model
{
    protected $fillable = ['nom_original', 'chemin', 'tache_id','uploaded_by'];

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
