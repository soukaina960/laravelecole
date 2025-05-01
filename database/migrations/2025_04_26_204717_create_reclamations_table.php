<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclamationsTable extends Migration
{
    public function up()
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('titre');
            $table->text('message');
            $table->string('statut')->default('en attente');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reclamations');
    }
}
