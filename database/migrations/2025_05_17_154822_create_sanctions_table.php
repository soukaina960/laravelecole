<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->string('type_sanction');
            $table->text('description');
            $table->integer('nombre_absences_min');
            $table->string('niveau_concerne');
            
            // Additional fields based on your model methods
            $table->unsignedBigInteger('etudiant_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('professeur_id')->nullable();
            
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('set null');
            $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('sanctions');
    }
};
