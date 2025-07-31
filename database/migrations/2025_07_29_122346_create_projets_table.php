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
            $table->text('details_importants')->nullable();
            $table->string('cahier_charge_path')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            // $table->string('reference_officielle')->nullable();
            $table->integer('avancement')->default(0);
            // $table->string('cadre_juridique')->nullable();
            $table->unsignedBigInteger('equipe_id')->nullable();
            $table->foreign('equipe_id')
                    ->references('id')
                    ->on('equipes')
                    ->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projets');
    }
};