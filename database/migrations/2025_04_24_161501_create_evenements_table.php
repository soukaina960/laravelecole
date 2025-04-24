<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration pour la table 'evenements'
Schema::create('evenements', function (Blueprint $table) {
    $table->id();
    $table->string('titre'); // Titre de l'événement
    $table->text('description'); // Description de l'événement
    $table->dateTime('date_debut'); // Date et heure de début
    $table->dateTime('date_fin'); // Date et heure de fin
    $table->string('lieu'); // Lieu de l'événement
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
