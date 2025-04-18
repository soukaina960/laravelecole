<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // database/migrations/xxxx_xx_xx_create_attestations_table.php

public function up()
{
    Schema::create('attestations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
        $table->string('annee_scolaire');
        $table->date('date_delivrance');
        $table->timestamps();
    });
}

};
