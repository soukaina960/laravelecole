<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRetardsTable extends Migration
{
    public function up()
    {
        Schema::table('retards', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('etudiant_id');
            $table->unsignedBigInteger('matiere_id')->nullable()->after('class_id');
            $table->unsignedBigInteger('professeur_id')->nullable()->after('matiere_id');

            // Si tu veux ajouter les foreign keys:
            $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('set null');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('set null');
            $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('retards', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['professeur_id']);

            $table->dropColumn('class_id');
            $table->dropColumn('matiere_id');
            $table->dropColumn('professeur_id');
        });
    }
}
