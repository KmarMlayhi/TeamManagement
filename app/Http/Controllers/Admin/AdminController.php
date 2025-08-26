<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Méthode pour calculer les stats utilisateurs
     */
    private function getUserStats()
    {
        $totalCollaborateurs = User::whereHas('role', fn($q) => $q->where('name', 'collaborateur'))->count();
        $pendingCollaborateurs = User::whereHas('role', fn($q) => $q->where('name', 'collaborateur'))
            ->where('is_validated', false)
            ->count();

        $totalChefs = User::whereHas('role', fn($q) => $q->where('name', 'chef_equipe'))->count();
        $pendingChefs = User::whereHas('role', fn($q) => $q->where('name', 'chef_equipe'))
            ->where('is_validated', false)
            ->count();

        return compact('totalCollaborateurs', 'pendingCollaborateurs', 'totalChefs', 'pendingChefs');
    }

    /**
     * Page d'accueil admin
     */
    public function home()
    {
        return view('admin.home', $this->getUserStats());
    }

    /**
     * Gestion des utilisateurs
     */
    public function usersManagement(Request $request)
    {
        $query = User::with(['role', 'grade', 'fonction', 'direction'])
            ->whereHas('role', function ($q) {
                $q->where('name', '<>', 'admin');
            })
            ->where('id', '<>', Auth::id())
            ->latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->paginate(5);

        return view('admin.users.management', array_merge(
            ['users' => $users],
            $this->getUserStats()
        ));
    }

    /**
     * Validation d'un utilisateur
     */
    public function validateUser(User $user)
    {
        $user->update(['is_validated' => true]);

        $roleName = $user->role ? $user->role->name : null;

        $message = match ($roleName) {
            'chef_equipe' => "Chef d'équipe validé avec succès : ",
            'collaborateur' => "Collaborateur validé avec succès : ",
            default => "Utilisateur validé avec succès : "
        };

        return redirect()->route('admin.users.management')
            ->with('success', $message . $user->name);
    }

    /**
     * Suppression d'un utilisateur
     */
    public function deleteUser(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.management')
            ->with('success', 'Utilisateur supprimé avec succès : ' . $user->name);
    }

    /**
     * Suspension d'un utilisateur
     */
    public function suspendUser(User $user)
    {
        $roleName = $user->role ? $user->role->name : null;

        if ($roleName === 'admin') {
            return back()->with('error', 'Impossible de suspendre un administrateur');
        }

        $user->update(['is_validated' => false]);

        $message = match ($roleName) {
            'chef_equipe' => "Chef d'équipe suspendu avec succès : ",
            'collaborateur' => "Collaborateur suspendu avec succès : ",
            default => "Utilisateur suspendu avec succès : "
        };

        return back()->with('success', $message . $user->name);
    }

    /**
     * Promotion d'un utilisateur en chef d'équipe
     */
    public function promoteToChef(User $user)
    {
        $roleChef = Role::where('name', 'chef_equipe')->first();

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
