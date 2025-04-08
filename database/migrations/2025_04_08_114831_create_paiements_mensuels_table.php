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
        Schema::create('paiements_mensuels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etudiant_id');
            $table->date('mois'); // ex : 2025-04-01
            $table->date('date_paiement')->nullable();
            $table->boolean('est_paye')->default(false);
            $table->timestamps();
        
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('cascade');
        });
        
    }
    public function down(): void
    {
        Schema::dropIfExists('paiements_mensuels');
    }
};
