<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('mot_de_passe');
<<<<<<< HEAD


            $table->enum('role', ['admin', 'professeur', 'super_surveillant' ,'surveillant', 'étudiant', 'parent']);
         



=======
            $table->enum('role', ['admin', 'professeur', 'surveillant', 'étudiant', 'parent']);
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
