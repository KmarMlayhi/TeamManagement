<?php

namespace App\Http\Controllers;

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
    $query = User::where('role', 'collaborateur')
                ->where('id', '<>', Auth::id());

    // Ajout de la recherche
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $users = $query->paginate(5);

    // Compteurs (adaptés si nécessaire avec la recherche)
    $totalUsers = User::where('role', 'collaborateur')->count();
    $pendingUsersCount = User::where('role', 'collaborateur')
                             ->where('is_validated', false)
                             ->count();
    $validatedUsersCount = User::where('role', 'collaborateur')
                              ->where('is_validated', true)
                              ->count();

    return view('admin.users.management', compact('users', 'totalUsers', 'pendingUsersCount', 'validatedUsersCount'));
}

    public function validateUser(User $user)
    {
        $user->is_validated = true;
        $user->save();

        return redirect()->route('admin.users.management')->with('success', 'Utilisateur validé avec succès : ' . $user->name);
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.management')->with('success', 'Utilisateur supprimé avec succès : ' . $user->name);
    }



    // Tu peux ajouter une méthode pour 'bloquer'/'désactiver' si tu as un statut différent de 'validé/non validé'
    public function toggleUserStatus(User $user)
    {
        // Exemple pour bloquer/débloquer :
        // Si tu as un champ 'is_active'
        // $user->is_active = !$user->is_active;
        // $user->save();
        // return redirect()->route('admin.home')->with('success', 'Statut utilisateur mis à jour.');
    }
}
