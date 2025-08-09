<?php 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $roleName = $user->role->name ?? null;

            // Bloquer si collaborateur ou chef_equipe non validé
            if (in_array($roleName, ['collaborateur', 'chef_equipe']) && !$user->is_validated) {
                Auth::logout(); // déconnexion immédiate
                return back()->withErrors([
                    'email' => 'Votre compte est en attente de validation.',
                ]);
            }

            // Redirection selon rôle
            return match ($roleName) {
                'admin'        => redirect()->intended('/admin/home'),
                'chef_equipe'  => redirect()->intended('/chef-equipe/dashboard'),
                'collaborateur'=> redirect()->intended('/collaborateur/home'),
                default        => redirect()->intended('/'),
            };
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
