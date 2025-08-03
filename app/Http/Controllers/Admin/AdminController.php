<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
    {   public function home()
{


    return view('admin.home');
}

    
public function usersManagement(Request $request)
{
    // Récupère tous les utilisateurs sauf admin
    $query = User::where('role', '<>', 'admin')
                ->where('id', '<>', Auth::id())
                ->latest();

    // Recherche
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $users = $query->paginate(5);

    // Compteurs
    $totalCollaborateurs = User::where('role', 'collaborateur')->count();
    $pendingCollaborateurs = User::where('role', 'collaborateur')
                               ->where('is_validated', false)
                               ->count();
    
    $totalChefs = User::where('role', 'chef_equipe')->count();
    $pendingChefs = User::where('role', 'chef_equipe')
                      ->where('is_validated', false)
                      ->count();

    return view('admin.users.management', compact(
        'users',
        'totalCollaborateurs',
        'pendingCollaborateurs',
        'totalChefs',
        'pendingChefs'
    ));
}

public function validateUser(User $user)
{
    $user->update(['is_validated' => true]);
    
    $message = match($user->role) {
        'chef_equipe' => "Chef d'équipe validé avec succès : ",
        default => "Collaborateur validé avec succès : "
    };

    return redirect()->route('admin.users.management')
                   ->with('success', $message . $user->name);
}

public function deleteUser(User $user)
{
        $user->delete();

        return redirect()->route('admin.users.management')->with('success', 'Utilisateur supprimé avec succès : ' . $user->name);
}
public function suspendUser(User $user)
{
    // Ne pas permettre de suspendre un admin
    if ($user->role === 'admin') {
        return back()->with('error', 'Impossible de suspendre un administrateur');
    }

    $user->update(['is_validated' => false]);
    
    $message = match($user->role) {
        'chef_equipe' => "Chef d'équipe suspendu avec succès : ",
        default => "Collaborateur suspendu avec succès : "
    };

    return back()->with('success', $message . $user->name);
}

public function promoteToChef(User $user)
{
    $user->update([
        'role' => 'chef_equipe',
        'is_validated' => false // Requiert une validation manuelle
    ]);
    
    return back()->with('success', "Utilisateur promu chef d'équipe : {$user->name}");
}


}
