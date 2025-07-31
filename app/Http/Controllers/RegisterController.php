<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,collaborateur,chef_equipe',
            'grade' => 'required|in:G1,G2,G3,G4,G5',
            'fonction' => 'required|string|max:100',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'grade'        => $request->grade,
            'fonction'     => $request->fonction,
            'is_validated' => $request->role === 'admin' ? true : false,
        ]);

        return redirect('/login')->with('success', 'Compte créé. En attente de validation si collaborateur ou chef equipe.');
    }
    
}

