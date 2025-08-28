<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('salles', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // ex: Salle A, Salle B
        $table->timestamps();
    });

    Schema::table('emplois_temps', function (Blueprint $table) {
        $table->foreignId('salle_id')->nullable()->constrained('salles')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('emplois_temps', function (Blueprint $table) {
        $table->dropForeign(['salle_id']);
        $table->dropColumn('salle_id');
    });

    Schema::dropIfExists('salles');
}

};
