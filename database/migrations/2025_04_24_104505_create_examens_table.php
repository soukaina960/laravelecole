<?php
// database/migrations/2024_04_24_000000_create_examens_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamensTable extends Migration
{
    public function up()
    {
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('professeur_id')->nullable()->constrained('professeurs')->onDelete('set null');
            $table->date('date'); // date de l'examen
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('salle')->nullable(); // exemple : "Salle A", "B1"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examens');
    }
}
