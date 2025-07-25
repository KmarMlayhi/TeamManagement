<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\LogoutController;



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
});


Route::middleware(['auth', 'isCollaborateur'])->group(function () {
    Route::get('/collaborateur/home', [CollaborateurController::class, 'home'])->name('collaborateur.home');
});


Route::middleware('guest')->group(function () {
    // Formulaire pour demander le lien de réinitialisation
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');

    // Envoi du lien par email
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Formulaire pour saisir le nouveau mot de passe via le token
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');

    // Traitement de la mise à jour du mot de passe
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});



Route::post('/logout', LogoutController::class)->name('logout');
