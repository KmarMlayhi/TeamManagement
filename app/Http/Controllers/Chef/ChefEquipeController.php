<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChefEquipeController extends Controller
{
     public function dashboard()
    {
        return view('chef_equipe.dashboard');
    }
}
