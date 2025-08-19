<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
     Schema::create('taches', function (Blueprint $table) {
    $table->id();
    $table->string('titre');
    $table->text('description');
    $table->date('date_debut');
    $table->date('date_fin_prevue');
    $table->date('date_fin_reelle')->nullable();
    $table->enum('priorite', ['basse', 'moyenne', 'haute', 'urgente']);
    $table->enum('statut', ['a_faire', 'en_cours', 'termine', 'bloque'])->default('a_faire');
    
    // Foreign key vers le projet
    $table->unsignedBigInteger('projet_id');
    $table->unsignedBigInteger('created_by'); // pour savoir qui a créé la tâche

    $table->timestamps();
    
    $table->foreign('projet_id')
          ->references('id')
          ->on('projets')
          ->onDelete('cascade');
          
    $table->foreign('created_by')
          ->references('id')
          ->on('users')
          ->onDelete('cascade');
}); 
}

public function down()
{
    Schema::table('taches', function (Blueprint $table) {
        $table->dropForeign(['projet_id']);
        $table->dropForeign(['affecte_a']);
    });
    Schema::dropIfExists('taches');
}
};