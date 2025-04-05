<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//ajoo
class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained()->onDelete('cascade');
            $table->text('contenu');
            $table->foreignId('envoyee_par')->constrained('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}
