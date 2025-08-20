<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        $roles = Role::all();
        $grades = Grade::all();
        $fonctions = Fonction::all();
        $directions = Direction::all();

        return view('auth.register', compact('roles', 'grades', 'fonctions', 'directions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => 'required|string|min:8|confirmed',
            'role_id'      => 'required|exists:roles,id',
            'grade_id'     => 'required|exists:grades,id',
            'fonction_id'  => 'required|exists:fonctions,id',
            'direction_id' => 'required|exists:directions,id',
        ]);

        // Récupérer le rôle pour savoir si c'est admin
        $role = Role::findOrFail($request->role_id);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role_id'      => $request->role_id,
            'grade_id'     => $request->grade_id,
            'fonction_id'  => $request->fonction_id,
            'direction_id' => $request->direction_id,
            'is_validated' => ($role->name === 'admin') ? true : false,
        ]);

        return redirect('/login')->with('success', 'Compte créé. En attente de validation si collaborateur ou chef équipe.');
    }
}


