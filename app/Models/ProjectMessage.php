<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'user_id',
        'message',
        'fichier',
        'fichier_original'
    ];

public function user()
{
    return $this->belongsTo(User::class);
}

public function projet()
{
    return $this->belongsTo(Projet::class);
}

}
