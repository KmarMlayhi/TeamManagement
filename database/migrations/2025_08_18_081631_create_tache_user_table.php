<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('tache_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tache_id')->index();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tache_user');
    }
};
