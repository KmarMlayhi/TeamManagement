<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Création de la table 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // nom de l'utilisateur
            $table->string('email')->unique();          // email unique
            $table->timestamp('email_verified_at')->nullable(); // date de vérification d'email
            $table->string('password');                 // mot de passe hashé

            // Champs personnalisés
            $table->string('role')->default('collaborateur'); // rôle: admin ou collaborateur
            $table->boolean('is_validated')->default(false);  // validé ou pas par un admin
            $table->string('avatar')->nullable();             // photo de profil
            $table->string('grade')->nullable(); // G1 à G5
            $table->string('fonction')->nullable(); // Fonction personnalisée            // poste dans l’entreprise

            $table->rememberToken();                // token pour "remember me"
            $table->timestamps();                   // created_at et updated_at
        });

        // Table pour les réinitialisations de mot de passe
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table pour stocker les sessions (utile si tu utilises la gestion des sessions dans la DB)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
