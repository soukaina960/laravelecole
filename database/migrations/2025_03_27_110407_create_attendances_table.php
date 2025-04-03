<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_03_26_000004_create_attendances_table.php
class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('etudiants')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'retard', 'exempte']);
            $table->text('notes')->nullable();
            $table->unique(['course_id', 'student_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}