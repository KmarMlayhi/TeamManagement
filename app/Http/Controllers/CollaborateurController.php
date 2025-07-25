<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollaborateurController extends Controller
{
   public function home()
{
    return view('collaborateur.home');
}

}
