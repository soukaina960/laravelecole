<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploisSurveillanceTable extends Migration
{
    public function up(): void
    {
        Schema::create('emploi_surveillances', function (Blueprint $table) {
            $table->id();
            $table->string('jour');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->foreignId('surveillant_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emploi_surveillances');
    }
}
//ajoo