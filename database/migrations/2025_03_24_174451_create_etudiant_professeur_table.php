<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // Database\Migrations\2025_xx_xx_create_etudiant_professeur_table.php
public function up()
{
    Schema::create('etudiant_professeur', function (Blueprint $table) {
        $table->id();
        $table->foreignId('etudiant_id')->constrained()->onDelete('cascade');
        $table->foreignId('professeur_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

};
