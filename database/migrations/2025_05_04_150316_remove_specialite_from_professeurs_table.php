<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->dropColumn('specialite');
        });
    }

    public function down(): void
    {
        Schema::table('professeurs', function (Blueprint $table) {
            $table->string('specialite')->nullable(); // adapte le type si besoin
        });
    }
};

