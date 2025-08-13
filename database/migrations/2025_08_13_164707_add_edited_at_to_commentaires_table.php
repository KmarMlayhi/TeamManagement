<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up()
{
    Schema::table('commentaires', function (Blueprint $table) {
        $table->timestamp('edited_at')->nullable();
    });
}

public function down()
{
    Schema::table('commentaires', function (Blueprint $table) {
        $table->dropColumn('edited_at');
    });
}

    /**
     * Run the migrations.
     */
    
};
