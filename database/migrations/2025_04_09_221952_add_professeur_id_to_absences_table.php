<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('absences', function (Blueprint $table) {
        $table->unsignedBigInteger('professeur_id')->nullable(); // Ajout de professeur_id
        $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('set null'); // Lier à la table des professeurs
    });
}

public function down()
{
    Schema::table('absences', function (Blueprint $table) {
        $table->dropForeign(['professeur_id']); // Supprimer la clé étrangère
        $table->dropColumn('professeur_id'); // Supprimer la colonne
    });
}

};
