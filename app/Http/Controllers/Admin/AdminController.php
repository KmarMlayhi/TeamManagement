<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function home()
    {
        return view('admin.home');
    }

public function usersManagement(Request $request)
{
    $query = User::with(['role', 'grade', 'fonction', 'direction'])  // Charger toutes les relations
        ->whereHas('role', function($q) {
            $q->where('name', '<>', 'admin');
        })
        ->where('id', '<>', Auth::id())
        ->latest();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $users = $query->paginate(5);

    // Compteurs...
    $totalCollaborateurs = User::whereHas('role', fn($q) => $q->where('name', 'collaborateur'))->count();
    $pendingCollaborateurs = User::whereHas('role', fn($q) => $q->where('name', 'collaborateur'))
                               ->where('is_validated', false)
                               ->count();

    $totalChefs = User::whereHas('role', fn($q) => $q->where('name', 'chef_equipe'))->count();
    $pendingChefs = User::whereHas('role', fn($q) => $q->where('name', 'chef_equipe'))
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

        $roleName = $user->role ? $user->role->name : null;

        $message = match($roleName) {
            'chef_equipe' => "Chef d'équipe validé avec succès : ",
            'collaborateur' => "Collaborateur validé avec succès : ",
            default => "Utilisateur validé avec succès : "
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
        $roleName = $user->role ? $user->role->name : null;

        if ($roleName === 'admin') {
            return back()->with('error', 'Impossible de suspendre un administrateur');
        }

        $user->update(['is_validated' => false]);

        $message = match($roleName) {
            'chef_equipe' => "Chef d'équipe suspendu avec succès : ",
            'collaborateur' => "Collaborateur suspendu avec succès : ",
            default => "Utilisateur suspendu avec succès : "
        };

        return back()->with('success', $message . $user->name);
    }

    public function promoteToChef(User $user)
    {
        $roleChef = \App\Models\Role::where('name', 'chef_equipe')->first();

        if (!$roleChef) {
            return back()->with('error', "Le rôle 'chef_equipe' n'existe pas dans la base.");
        }

        $user->update([
            'role_id' => $roleChef->id,
            'is_validated' => false // Requiert une validation manuelle
        ]);

        return back()->with('success', "Utilisateur promu chef d'équipe : {$user->name}");
    }
}
