<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMatiereIdToFichiersTable extends Migration
{
    public function up()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            $table->unsignedBigInteger('matiere_id')->after('id');
            $table->foreign('matiere_id')
                  ->references('id')
                  ->on('matieres')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            $table->dropForeign(['matiere_id']);
            $table->dropColumn('matiere_id');
        });
    }
}