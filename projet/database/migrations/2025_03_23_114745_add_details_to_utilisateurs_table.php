<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('photo_profil')->nullable(); // Pour stocker l'URL de la photo
        });
    }

    public function down()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'adresse', 'photo_profil']);
        });
    }
};
