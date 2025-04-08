<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementMensuelsTable extends Migration
{
    public function up()
    {
        Schema::create('paiement_mensuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained(); // Associe l'étudiant
            $table->date('mois');
            $table->boolean('est_paye')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiement_mensuels');
    }
}
