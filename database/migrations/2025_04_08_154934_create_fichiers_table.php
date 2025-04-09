<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichiersTable extends Migration
{
    public function up()
    {
        Schema::create('fichiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professeur_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('semestre_id');
            $table->string('type_fichier'); // exemple : cours, devoir, examen
            $table->string('nom_fichier');
            $table->string('chemin_fichier'); // chemin du fichier sur le serveur
            $table->timestamps();

            // Clés étrangères
            $table->foreign('professeur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classrooms')->onDelete('cascade');
            $table->foreign('semestre_id')->references('id')->on('semestres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fichiers');
    }
}

