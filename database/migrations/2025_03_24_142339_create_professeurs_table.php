<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('professeurs', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('user_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('specialite'); // Spécialité du professeur
            $table->string('niveau_enseignement'); // Niveau d'enseignement (primaire, secondaire, etc.)
            $table->string('diplome'); // Diplôme du professeur
            $table->date('date_embauche'); // Date d'embauche
            $table->timestamps(); // Champs created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('professeurs');
    }
};
