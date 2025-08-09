<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableAddRoleId extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la colonne role si elle existe
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            // Ajouter role_id comme clé étrangère (nullable temporairement pour éviter problème si données)
            $table->unsignedBigInteger('role_id')->nullable()->after('email');

            // Ajouter la contrainte FK
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            // Réajouter la colonne role texte
            $table->string('role')->after('email');
        });
    }
}
