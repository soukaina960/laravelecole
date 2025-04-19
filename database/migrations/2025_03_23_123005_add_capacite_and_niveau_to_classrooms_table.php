<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->integer('capacite')->after('name'); // Ajoute une colonne 'capacite'
            $table->string('niveau')->after('capacite'); // Ajoute une colonne 'niveau'
        });
    }

    public function down()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('capacite');
            $table->dropColumn('niveau');
        });
    }
};
