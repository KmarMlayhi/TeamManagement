<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Si collaborateur non validé
            if ($user->role === 'collaborateur' && !$user->is_validated) {
                Auth::logout(); // déconnexion immédiate
                return back()->withErrors([
                    'email' => 'Votre compte est en attente de validation.',
                ]);
            }

            // Redirection selon rôle
            return redirect()->intended($user->role === 'admin' ? '/admin/home' : '/collaborateur/home');
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
