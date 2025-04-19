<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_03_26_000003_create_grades_table.php
class CreateGradesTable extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('etudiants')->onDelete('cascade');
            $table->decimal('note', 5, 2);
            $table->text('commentaires')->nullable();
            $table->timestamp('date_attribution')->useCurrent();
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
}