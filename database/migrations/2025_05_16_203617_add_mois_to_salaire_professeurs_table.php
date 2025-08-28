<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('salaire_professeurs', function (Blueprint $table) {
        $table->string('mois', 2)->after('annee');
    });
}

public function down()
{
    Schema::table('salaire_professeurs', function (Blueprint $table) {
        $table->dropColumn('mois');
    });
}

};
