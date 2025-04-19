<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration

{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etudiant_id');
            $table->unsignedBigInteger('professeur_id');
            $table->unsignedBigInteger('annee_scolaire_id');
        
            $table->decimal('note1', 5, 2)->nullable();
            $table->decimal('note2', 5, 2)->nullable();
            $table->decimal('note3', 5, 2)->nullable();
            $table->decimal('note4', 5, 2)->nullable();
            $table->decimal('facteur', 5, 2)->default(1);
            $table->text('remarque')->nullable();
        
            $table->timestamps();
        
            // Les clés étrangères
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('cascade');
            $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('cascade');
            $table->foreign('annee_scolaire_id')->references('id')->on('annees_scolaires')->onDelete('cascade');
        });
    }        

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
}
