<?php

// database/migrations/xxxx_xx_xx_update_emplois_temps_table_add_creneau_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmploisTempsTableAddCreneauId extends Migration
{
    public function up()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->dropColumn(['heure_debut', 'heure_fin']);
            $table->unsignedBigInteger('creneau_id')->after('jour');

            $table->foreign('creneau_id')->references('id')->on('creneaux')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('emplois_temps', function (Blueprint $table) {
            $table->dropForeign(['creneau_id']);
            $table->dropColumn('creneau_id');
            $table->time('heure_debut');
            $table->time('heure_fin');
        });
    }
}
