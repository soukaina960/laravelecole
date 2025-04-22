<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMatiereIdToEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Ajouter la colonne matiere_id
            $table->unsignedBigInteger('matiere_id')->nullable()->after('annee_scolaire_id');
            
            // Ajouter la contrainte de clé étrangère
            $table->foreign('matiere_id')
                  ->references('id')
                  ->on('matieres')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère d'abord
            $table->dropForeign(['matiere_id']);
            
            // Supprimer la colonne
            $table->dropColumn('matiere_id');
        });
    }
}