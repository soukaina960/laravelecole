<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etudiant_id');  // علاقة مع الطالب
            $table->unsignedBigInteger('semestre_id'); 
            $table->unsignedBigInteger('annee_scolaire_id'); // علاقة مع السنة الدراسية
            $table->decimal('moyenne_generale', 5, 2);  // المعدل العام
            $table->boolean('est_traite')->default(false); // حالة المعالجة (واش تم المعالجة أو لا)
            $table->timestamps();  // أوقات الإنشاء والتحديث
    
            // إضافة العلاقات مع الجداول الأخرى
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('cascade');
            $table->foreign('semestre_id')->references('id')->on('semestres')->onDelete('cascade');
            $table->foreign('annee_scolaire_id')->references('id')->on('annees_scolaires')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('bulletins');
    }
    
};