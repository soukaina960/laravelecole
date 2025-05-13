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
        Schema::create('statistiques_mensuelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mois');
            $table->unsignedInteger('annee');
            $table->integer('etudiants');
            $table->integer('professeurs');
            $table->integer('classes');
            $table->decimal('revenus', 10, 2);
            $table->decimal('depenses', 10, 2);
            $table->decimal('salaires', 10, 2);
            $table->decimal('charges', 10, 2);
            $table->integer('alertes');
            $table->decimal('reste', 10, 2);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistiques_mensuelles');
    }
};
