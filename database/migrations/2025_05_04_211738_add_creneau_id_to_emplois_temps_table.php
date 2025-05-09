<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreneauIdToEmploisTempsTable extends Migration
{
    public function up()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->unsignedBigInteger('creneau_id')->nullable(); // Ajout du champ creneau_id
            $table->foreign('creneau_id')->references('id')->on('creneaux')->onDelete('cascade'); // Définir la clé étrangère
        });
    }

    public function down()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->dropForeign(['creneau_id']);
            $table->dropColumn('creneau_id');
        });
    }
}
