<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->string('nom')->after('id');  // Ajout du champ nom
            $table->string('email')->unique()->after('nom');  // Ajout du champ email avec contrainte d'unicité
            $table->decimal('montant', 10, 2)->nullable()->after('niveau_enseignement');  // Montant du professeur
        });
    }

    public function down()
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->dropColumn('nom');
            $table->dropColumn('email');
            $table->dropColumn('montant');
        });
    }
};