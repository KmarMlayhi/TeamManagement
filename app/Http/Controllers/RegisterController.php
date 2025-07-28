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
            'role'     => 'required|in:admin,collaborateur',
            'poste'    => 'required|string|max:255',
        ]);
         // Vérifier s’il y a déjà un admin
    if ($request->role === 'admin' && User::where('role', 'admin')->exists()) {
        throw ValidationException::withMessages([
            'role' => 'Un administrateur existe déjà.',
        ]);
    }

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'poste'        => $request->poste,
            'is_validated' => $request->role === 'admin' ? true : false,
        ]);

        return redirect('/login')->with('success', 'Compte créé. En attente de validation si collaborateur.');
    }
    
}

