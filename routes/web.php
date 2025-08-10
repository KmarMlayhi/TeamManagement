<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsChefEquipe;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsCollaborateyr;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Chef\EquipeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Chef\TacheController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Chef\ProjetController;
use App\Http\Controllers\Collaborateur\CollaborateurController;
use App\Http\Controllers\Chef\ChefEquipeController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;



Route::get('/Welcome', function () {
    return view('welcome');
});
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

//Routes Admin 

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('/admin/users', [AdminController::class, 'usersManagement'])->name('admin.users.management');
    // Les routes pour valider et supprimer un utilisateur spécifique
    Route::post('/admin/users/{user}/validate', [AdminController::class, 'validateUser'])->name('admin.users.validate');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspendUser'])
     ->name('admin.users.suspend');
});


 // Routes Chef 
Route::middleware(['auth', 'isChefEquipe'])->prefix('chef-equipe')->name('chef_equipe.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ChefEquipeController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des équipes
    Route::prefix('equipes')->name('equipes.')->group(function () {
        Route::get('/', [EquipeController::class, 'index'])->name('index');
        Route::get('/create', [EquipeController::class, 'create'])->name('create');
        Route::post('/', [EquipeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [EquipeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EquipeController::class, 'update'])->name('update');
        Route::delete('/{id}', [EquipeController::class, 'destroy'])->name('destroy');
        Route::get('/{equipe}/details', [EquipeController::class, 'details'])->name('chef_equipe.equipes.details');
    });
    
    // Gestion des projets
    Route::prefix('projets')->name('projets.')->group(function () {
        Route::get('/', [ProjetController::class, 'index'])->name('index');
        Route::get('/create', [ProjetController::class, 'create'])->name('create');
        Route::post('/', [ProjetController::class, 'store'])->name('store');
        Route::get('/{projet}/details', [ProjetController::class, 'details'])->name('chef_equipe.projets.details');
        Route::get('/{projet}/edit', [ProjetController::class, 'edit'])->name('edit');
        Route::put('/{projet}', [ProjetController::class, 'update'])->name('update');
        Route::delete('/{projet}', [ProjetController::class, 'destroy'])->name('destroy');
        
        
         Route::prefix('{projet}/taches')->name('taches.')->group(function () {
            Route::get('/', [TacheController::class, 'index'])->name('index');
            Route::get('/create', [TacheController::class, 'create'])->name('create');
            Route::post('/', [TacheController::class, 'store'])->name('store');
            Route::get('/{tache}/edit', [TacheController::class, 'edit'])->name('edit');
            Route::put('/{tache}', [TacheController::class, 'update'])->name('update');
            Route::delete('/{tache}', [TacheController::class, 'destroy'])->name('destroy');
            
            // Routes supplémentaires pour Kanban
            Route::get('/kanban', [TacheController::class, 'kanban'])->name('kanban');
            Route::post('/update-status', [TacheController::class, 'updateStatus'])->name('update-status');
            Route::post('/reorder', [TacheController::class, 'reorder'])->name('reorder');
        });
    
    });
    Route::delete('documents/{document}', [ProjetController::class, 'destroyDocument'])
                ->name('documents.destroy');
});

//Routes Collaborateurs 
Route::middleware(['auth', 'isCollaborateur'])->group(function () {
    // Tableau de bord collaborateur
    Route::get('/collaborateur/home', [CollaborateurController::class, 'home'])->name('collaborateur.home');

    // Accès aux équipes (affichage uniquement)
    Route::get('/collaborateur/equipes', [CollaborateurController::class, 'equipesIndex'])->name('collaborateur.equipes.index');
    Route::get('/collaborateur/equipes/{equipe}', [CollaborateurController::class, 'equipesShow'])->name('collaborateur.equipes.show');

    // Accès aux projets (affichage uniquement)
    Route::get('/collaborateur/projets', [CollaborateurController::class, 'index'])->name('collaborateur.projets.index');
    Route::get('/collaborateur/projets/{projet}', [CollaborateurController::class, 'show'])->name('collaborateur.projets.show');
    Route::get('/collaborateur/projets/{projet}/details', [CollaborateurController::class, 'projetDetails']);
    Route::get('/collaborateur/projets/{projet}/taches', [CollaborateurController::class, 'projetTaches'])
     ->name('collaborateur.projets.taches');
    
    Route::get('/collaborateur/taches/{tache}', [CollaborateurController::class, 'showTache'])
     ->name('collaborateur.taches.show');
    // Gérer profil du collaborateur
    Route::get('/collaborateur/profil', [CollaborateurController::class, 'edit'])->name('collaborateur.profil');
    Route::put('/collaborateur/profil', [CollaborateurController::class, 'update']);
});

   

// Formulaire pour demander le lien de réinitialisation
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    // Envoi du lien par email
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    // Traitement de la mise à jour du mot de passe
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');



Route::post('/logout', LogoutController::class)->name('logout');



