<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->date('date_fin_reelle')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'suspendu'])->default('en_attente');
            $table->string('client')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->integer('avancement')->default(0);
            $table->unsignedBigInteger('equipe_id')->nullable();
            $table->foreign('equipe_id')
                    ->references('id')
                    ->on('equipes')
                    ->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Création de la table pour les documents
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('chemin');
            $table->string('type');
            $table->unsignedInteger('taille');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('projets');
    }
};