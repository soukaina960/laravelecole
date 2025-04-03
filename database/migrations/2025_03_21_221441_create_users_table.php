<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('nom_complet');
            $table->string('email')->unique();
            $table->string('mot_de_passe');
            $table->string('telephone', 20);
            $table->string('adresse')->nullable();
            $table->string('photo_profil')->nullable();
            $table->enum('role', ['admin', 'professeur', 'surveillant', 'etudiant', 'parent']);
            $table->boolean('statut_compte')->default(true); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}

