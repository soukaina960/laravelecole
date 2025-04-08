<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnneesScolairesTable extends Migration
{
    public function up()
    {
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id(); // clé primaire
            $table->string('annee');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('annees_scolaires');
    }
}
