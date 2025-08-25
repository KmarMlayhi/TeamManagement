<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('project_messages', function (Blueprint $table) {
        $table->string('fichier_original')->nullable()->after('fichier');
    });
}

public function down(): void
{
    Schema::table('project_messages', function (Blueprint $table) {
        $table->dropColumn('fichier_original');
    });
}

};
