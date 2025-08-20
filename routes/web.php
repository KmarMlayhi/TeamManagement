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
use App\Http\Controllers\Chef\ChatController;
use App\Http\Controllers\Chef\ChefEquipeCommentaireController;
use App\Http\Controllers\Collaborateur\CollaborateurController;
use App\Http\Controllers\Collaborateur\TacheCommentaireController;
use App\Http\Controllers\Chef\ChefEquipeController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Chef\MeetingController;




Route::get('/Welcome', function () {
    return view('welcome');
});
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);



Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
    Route::post('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

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
Route::middleware(['auth', 'isChefEquipe'])
->prefix('chef-equipe')
->name('chef_equipe.')
->group(function () {
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
        Route::get('/{projet}/reunion', [MeetingController::class, 'createMeeting'])
        ->name('reunion');

         Route::prefix('{projet}/taches')->name('taches.')->group(function () {
            Route::get('/', [TacheController::class, 'index'])->name('index');
            Route::get('/create', [TacheController::class, 'create'])->name('create');
            Route::post('/', [TacheController::class, 'store'])->name('store');
            Route::get('/{tache}/edit', [TacheController::class, 'edit'])->name('edit');
            Route::put('/{tache}', [TacheController::class, 'update'])->name('update');
            Route::delete('/{tache}', [TacheController::class, 'destroy'])->name('destroy');
            Route::post('/{tache}/upload-document', [TacheController::class, 'uploadDocument'])
                        ->name('uploadDocument');

            
            // Routes supplémentaires pour Kanban
            Route::get('/kanban', [TacheController::class, 'kanban'])->name('kanban');
            Route::post('/update-status', [TacheController::class, 'updateStatus'])->name('update-status');
            Route::post('/reorder', [TacheController::class, 'reorder'])->name('reorder');
        });
    
    });

    // Route::get('/reunion/create', [MeetingController::class, 'createMeeting'])
    //         ->name('reunion.create');


    // Gestion des documents d'une tâche
            Route::delete('/documents/{taskdocument}', [TacheController::class, 'destroyDocument'])->name('documents.destroy');
    Route::delete('documents/{document}', [ProjetController::class, 'destroyDocument'])
                ->name('documents.destroy');

    Route::get('/commentaires', [ChefEquipeCommentaireController::class, 'index'])
        ->name('commentaires');

    Route::post('/commentaires/{commentaire}/repondre', [ChefEquipeCommentaireController::class, 'repondre'])
        ->name('commentaires.repondre');

    Route::delete('/commentaires/{commentaire}', [ChefEquipeCommentaireController::class, 'destroy'])
        ->name('commentaires.destroy');
    Route::get('commentaires/{commentaire}/edit', [ChefEquipeCommentaireController::class, 'edit'])->name('chef.commentaire.edit');
    Route::patch('commentaires/{commentaire}', [ChefEquipeCommentaireController::class, 'update'])->name('chef.commentaire.update');
    // Kanban par projet
    // Chef équipe : liste des projets où il est membre
    Route::get('/suivi', [ChefEquipeController::class, 'suivi'])->name('suivi');

    // Kanban d'un projet
    Route::get('/kanban/{projet}', [ChefEquipeController::class, 'kanbanProjet'])->name('kanban');
   
    // Liste des projets pour lesquels l'utilisateur peut discuter
    Route::get('/projets/discussions', [ChatController::class, 'projetsDiscussion'])
        ->name('project_messages.projet_list');

    // Afficher les messages d’un projet spécifique
    Route::get('/projets/{projet}/messages', [ChatController::class, 'showProjetMessages'])
        ->name('project_messages.index');

    // Ajouter un message à un projet spécifique
    Route::post('/projets/{projet}/messages', [ChatController::class, 'storeMessage'])
        ->name('project_messages.store');
        Route::get('/projet/{projet}/users', [ChatController::class,'projectUsers'])
     ->name('project_users');

});

//Routes Collaborateurs 
Route::middleware(['auth', 'isCollaborateur'])->group(function () {
    // Tableau de bord collaborateur
    Route::get('/collaborateur/home', [CollaborateurController::class, 'home'])->name('collaborateur.home');
    Route::get('/collaborateur/notification/{id}/read', [CollaborateurController::class, 'markNotificationRead'])
    ->name('collaborateur.notification.read');


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
    Route::put('/collaborateur/taches/{tache}/statut', [CollaborateurController::class, 'updateStatut'])
    ->name('collaborateur.taches.updateStatut');

    // Vue Kanban des tâches pour collaborateur ( ses propres taches)
    Route::get('/collaborateur/projets/{projet}/taches/kanban', [CollaborateurController::class, 'projetTachesKanban'])
    ->name('collaborateur.projets.taches.kanban');
    // Route pour la mise à jour du statut via Kanban
    Route::post('/collaborateur/taches/update-statut-kanban', [CollaborateurController::class, 'updateStatutKanban'])
        ->name('collaborateur.taches.updateStatutKanban');
    // Page Suivi
        Route::get('/collaborateur/suivi', [CollaborateurController::class, 'suivi'])
        ->name('collaborateur.suivi');
    // Kanban d'un projet
    Route::get('/collaborateur/suivi/projet/{projet}', [CollaborateurController::class, 'kanbanProjet'])
        ->name('collaborateur.suivi.kanban');
        // Gérer profil du collaborateur
    Route::prefix('collaborateur/taches/{tache}')->group(function() {
    Route::get('/commentaires', [TacheCommentaireController::class, 'index'])->name('collaborateur.taches.commentaires.index');
    Route::post('/commentaires', [TacheCommentaireController::class, 'store'])->name('collaborateur.taches.commentaires.store');
    Route::put('/{commentaire}', [TacheCommentaireController::class, 'update']);
    Route::delete('/{commentaire}', [TacheCommentaireController::class, 'destroy']);
    Route::get('/documents', [CollaborateurController::class, 'getDocuments'])
        ->name('collaborateur.taches.documents');
    Route::post('/documents', [CollaborateurController::class, 'uploadDocument'])
        ->name('collaborateur.taches.documents.upload');
    Route::delete('/documents/{document}', [CollaborateurController::class, 'deleteDocument'])
        ->name('collaborateur.taches.documents.delete');

    });
     // Liste des projets pour lesquels le collaborateur peut discuter
    Route::get('/projets/discussions', [App\Http\Controllers\Collaborateur\ChatController::class, 'projetsDiscussion'])
        ->name('projet_messages.projet_list');

    // Afficher les messages d’un projet spécifique
    Route::get('/projets/{projet}/messages', [App\Http\Controllers\Collaborateur\ChatController::class, 'showProjetMessages'])
        ->name('projet_messages.index');

    // Ajouter un message à un projet spécifique
    Route::post('/projets/{projet}/messages', [App\Http\Controllers\Collaborateur\ChatController::class, 'storeMessage'])
        ->name('projet_messages.store');
    Route::get('/projet/{projet}/users', [App\Http\Controllers\Collaborateur\ChatController::class,'projectUsers'])
     ->name('projet.users');
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



