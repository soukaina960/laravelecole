<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHeureDebutHeureFinFromEmploisTemps extends Migration
{
    /**
     * Exécuter la migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->dropColumn('heure_debut');
            $table->dropColumn('heure_fin');
        });
    }

    /**
     * Annuler la migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
        });
    }
}
