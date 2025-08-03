<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_equipes_table.php
    public function up()
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->unsignedBigInteger('parent_id')->nullable(); // Pour la sous-équipe
            $table->unsignedInteger('niveau')->default(1);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('equipes')->onDelete('set null');        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipes');
    }
};
