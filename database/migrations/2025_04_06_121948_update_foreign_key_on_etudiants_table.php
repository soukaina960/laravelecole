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
        Schema::table('etudiants', function (Blueprint $table) {
            // Supprimer la contrainte de la clé étrangère existante
            $table->dropForeign(['classe_id']);
            
            // Ajouter une nouvelle contrainte avec la bonne table et la suppression en cascade
            $table->foreignId('classe_id')->constrained('classrooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Supprimer la contrainte si on annule la migration
            $table->dropForeign(['classe_id']);
            
            // Recréer la contrainte précédente si nécessaire
            $table->foreignId('classe_id')->constrained();  // Si tu veux revenir à l'état initial
        });
    }
};
