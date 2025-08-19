<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taskdocuments', function (Blueprint $table) {
            $table->id();
            $table->string('nom_original');
            $table->string('chemin');
            $table->unsignedBigInteger('tache_id');
            $table->timestamps();

            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
        });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taskdocuments');
    }
};
