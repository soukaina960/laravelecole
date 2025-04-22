<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('absences', function (Blueprint $table) {
        $table->unsignedBigInteger('class_id')->nullable(); // Ajout de la colonne class_id
        $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('set null'); // Clé étrangère vers la table classes
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            //
        });
    }
};
