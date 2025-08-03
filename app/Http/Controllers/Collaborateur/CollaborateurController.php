<?php

namespace App\Http\Controllers\Collaborateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollaborateurController extends Controller
{
   public function home()
{
    return view('collaborateur.home');
}

}
