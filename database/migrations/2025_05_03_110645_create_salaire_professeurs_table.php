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
        Schema::create('salaire_professeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained()->onDelete('cascade');
            
         // format '2025-04'
            $table->decimal('total_paiements', 10, 2);
            $table->decimal('salaire', 10, 2);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaire_professeurs');
    }
};
