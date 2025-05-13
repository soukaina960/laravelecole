<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnneeToSalaireProfesseursTable extends Migration
{
    public function up()
    {
        Schema::table('salaire_professeurs', function (Blueprint $table) {
            $table->unsignedSmallInteger('annee'); // Ex: 2025
        });
    }

    public function down()
    {
        Schema::table('salaire_professeurs', function (Blueprint $table) {
            $table->dropColumn('annee');
        });
    }
}
