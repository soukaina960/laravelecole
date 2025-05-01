<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSalleAndAddJourToExamensTable extends Migration
{
    public function up()
    {
        Schema::table('examens', function (Blueprint $table) {
            // Supprimer la colonne salle
            $table->dropColumn('salle');
            
            // Ajouter la colonne jour
            $table->string('jour')->after('date');
        });
    }

    public function down()
    {
        Schema::table('examens', function (Blueprint $table) {
            // Pour rollback, on recrée salle et on supprime jour
            $table->string('salle')->nullable();
            $table->dropColumn('jour');
        });
    }
}