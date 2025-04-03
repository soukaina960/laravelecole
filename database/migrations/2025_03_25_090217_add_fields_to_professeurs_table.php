<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->decimal('prime', 10, 2)->default(0)->after('montant');   // Prime ajoutée
            $table->decimal('pourcentage', 5, 2)->default(0)->after('prime');  // Pourcentage de réduction selon les étudiants
            $table->decimal('total', 10, 2)->default(0)->after('pourcentage'); // Total calculé
        });
    }

    public function down()
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->dropColumn(['nom', 'email', 'montant', 'prime', 'pourcentage', 'total']);
        });
    }
};
