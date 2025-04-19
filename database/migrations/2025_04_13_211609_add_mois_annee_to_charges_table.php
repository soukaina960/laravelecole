<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->integer('mois')->after('montant');
            $table->integer('annee')->after('mois');
        });
    }
    
    public function down()
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn(['mois', 'annee']);
        });
    }
    
};
