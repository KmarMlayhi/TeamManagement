<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auteur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destinataire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tache_id')->nullable()->constrained('taches')->onDelete('cascade');
            $table->foreignId('projet_id')->nullable()->constrained('projets')->onDelete('cascade');
            $table->text('contenu');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            // On peut ajouter un index composite pour optimiser les recherches
            $table->index(['tache_id', 'projet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};
