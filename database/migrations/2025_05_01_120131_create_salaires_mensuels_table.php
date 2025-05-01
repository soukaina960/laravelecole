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
        Schema::create('salaires_mensuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('mois');
            $table->year('annee');
            $table->decimal('montant_total_paye', 10, 2); // total des paiements des élèves
            $table->decimal('prime', 10, 2);
            $table->decimal('pourcentage', 5, 2);
            $table->decimal('salaire_calcule', 10, 2); // salaire final
            $table->timestamps();
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaires_mensuels');
    }
};
