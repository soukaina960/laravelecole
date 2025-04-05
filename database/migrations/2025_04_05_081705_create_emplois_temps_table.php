<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emplois_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classe_id');
            $table->string('jour', 10); 
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('matiere');
            $table->unsignedBigInteger('professeur_id');
            $table->string('salle', 20);
            $table->timestamps(); 

            // Clés étrangères
            $table->foreign('classe_id')->references('id')->on('classes');
            $table->foreign('professeur_id')->references('id')->on('professeurs');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emplois_temps');
    }
};