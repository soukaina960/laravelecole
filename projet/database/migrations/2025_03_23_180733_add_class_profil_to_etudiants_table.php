<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Modifie la colonne photo_profil pour en faire un BLOB
            $table->binary('photo_profil')->change();
        });
    }

    public function down()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Restaure le type d'origine de la colonne (par exemple, TEXT)
            $table->text('photo_profil')->change();
        });
    }
};
