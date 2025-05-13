<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfesseurForeignInFichiersTable extends Migration
{
    public function up()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            // Supprimer la contrainte en utilisant le nom de la colonne
            $table->dropForeign(['professeur_id']);

            // Ajouter la nouvelle contrainte
            $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            $table->dropForeign(['professeur_id']);

            $table->foreign('professeur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }
}
