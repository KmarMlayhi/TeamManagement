<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsChefEquipe;
use App\Http\Middleware\IsCollaborateyr;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\Admin\ProjetController;
use App\Http\Controllers\Admin\TacheController;



Route::get('/Welcome', function () {
    return view('welcome');
});
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('/admin/users', [AdminController::class, 'usersManagement'])->name('admin.users.management');
    // Les routes pour valider et supprimer un utilisateur spécifique
    Route::post('/admin/users/{user}/validate', [AdminController::class, 'validateUser'])->name('admin.users.validate');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspendUser'])
     ->name('admin.users.suspend');
    Route::get('/admin/equipe', [EquipeController::class, 'index'])->name('equipes.index');
    Route::get('/admin/equipe/create', [EquipeController::class, 'create'])->name('equipes.create');
    Route::post('/admin/equipe/store', [EquipeController::class, 'store'])->name('equipes.store');
    Route::delete('/admin/equipe/{id}', [EquipeController::class, 'destroy'])->name('equipes.destroy');
    Route::get('/admin/equipe/{id}/edit', [EquipeController::class, 'edit'])->name('equipe.edit');
    Route::put('/admin/equipe/{id}', [EquipeController::class, 'update'])->name('equipe.update');


});
// Routes pour les projets
Route::prefix('admin/projets')->name('admin.projets.')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/', [ProjetController::class, 'index'])->name('index');
    Route::get('/create', [ProjetController::class, 'create'])->name('create');
    Route::post('/', [ProjetController::class, 'store'])->name('store');
    Route::get('/{projet}', [ProjetController::class, 'show'])->name('show');
    Route::get('/{projet}/edit', [ProjetController::class, 'edit'])->name('edit');
    Route::put('/{projet}', [ProjetController::class, 'update'])->name('update');
    Route::delete('/{projet}', [ProjetController::class, 'destroy'])->name('destroy');
    
    // Routes pour les tâches
    Route::prefix('/{projet}/taches')->name('taches.')->group(function () {
        Route::get('/', [TacheController::class, 'index'])->name('index');
        Route::get('/create', [TacheController::class, 'create'])->name('create');
        Route::post('/', [TacheController::class, 'store'])->name('store');
        Route::get('/{tache}/edit', [TacheController::class, 'edit'])->name('edit');
        Route::put('/{tache}', [TacheController::class, 'update'])->name('update');
        Route::delete('/{tache}', [TacheController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'isCollaborateur'])->group(function () {
    Route::get('/collaborateur/home', [CollaborateurController::class, 'home'])->name('collaborateur.home');
});
   
// Route::middleware('guest')->group(function () {
    // Formulaire pour demander le lien de réinitialisation
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    // Envoi du lien par email
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    // Traitement de la mise à jour du mot de passe
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');

// });

Route::post('/logout', LogoutController::class)->name('logout');



