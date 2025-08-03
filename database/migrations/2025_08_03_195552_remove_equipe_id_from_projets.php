<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('projets', function (Blueprint $table) {
        $table->dropForeign(['equipe_id']);
        $table->dropColumn('equipe_id');
    });
}

public function down()
{
    Schema::table('projets', function (Blueprint $table) {
        $table->foreignId('equipe_id')->constrained()->onDelete('set null');
    });
}
};
