<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('project_messages', function (Blueprint $table) {
        $table->string('fichier')->nullable()->after('message');
    });
}

public function down()
{
    Schema::table('project_messages', function (Blueprint $table) {
        $table->dropColumn('fichier');
    });
}

};
