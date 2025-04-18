<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropNoteMatiereTable extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('note_matiere');
    }

    public function down(): void
    {
        // Si besoin, tu peux remettre la structure initiale ici
        Schema::create('note_matiere', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etudiant_id');
            $table->unsignedBigInteger('professeur_id');
            $table->decimal('note_finale', 5, 2);
            $table->timestamps();
        });
    }
}
