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
        Schema::table('absences', function (Blueprint $table) {
            $table->unsignedBigInteger('matiere_id')->nullable(); // Ajout de la colonne class_id
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('set null'); // Clé étrangère vers la table classes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            //
        });
    }
};
