<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('equipes', function (Blueprint $table) {
        // Ajoutez d'abord la colonne sans contrainte
        $table->unsignedBigInteger('created_by')->after('id');
        
        // Puis ajoutez la contrainte
        $table->foreign('created_by')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('equipes', function (Blueprint $table) {
        $table->dropForeign(['created_by']);
        $table->dropColumn('created_by');
    });
}
};
