<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projet_equipe', function (Blueprint $table) {
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipe_id')->constrained()->onDelete('cascade');
            $table->primary(['projet_id', 'equipe_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('projet_equipe');
    }
};
