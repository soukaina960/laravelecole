<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->unsignedBigInteger('filiere_id')->nullable()->after('id');
    });

    // Puis ajoute la contrainte séparément
    Schema::table('classrooms', function (Blueprint $table) {
        $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('cascade');
    });
}


};
