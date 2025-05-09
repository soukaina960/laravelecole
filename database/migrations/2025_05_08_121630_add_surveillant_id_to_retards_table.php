<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retards', function (Blueprint $table) {
            $table->unsignedBigInteger('surveillant_id')->nullable()->after('id');

            // Si tu veux ajouter une contrainte de clé étrangère :
            $table->foreign('surveillant_id')->references('id')->on('surveillant')->onDelete('set null');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retards', function (Blueprint $table) {
            $table->dropForeign(['surveillant_id']);
            $table->dropColumn('surveillant_id');
        });
    }
};
