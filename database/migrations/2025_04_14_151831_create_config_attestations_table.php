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
        // database/migrations/xxxx_xx_xx_create_config_attestations_table.php
Schema::create('config_attestations', function (Blueprint $table) {
    $table->id();
    $table->string('nom_ecole');
    $table->string('nom_faculte')->nullable();
    $table->string('annee_scolaire');
    $table->string('telephone')->nullable();
    $table->string('fax')->nullable();
    $table->string('logo_path')->nullable(); // pour uploader un logo
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_attestations');
    }
};
