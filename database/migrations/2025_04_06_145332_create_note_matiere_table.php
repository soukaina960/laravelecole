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
        Schema::create('note_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('professeur_id')->constrained('professeur')->onDelete('cascade');
            $table->decimal('note_finale', 5, 2); // Note finale avec une précision de 2 décimales
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('note_matiere');
    }
    
};
