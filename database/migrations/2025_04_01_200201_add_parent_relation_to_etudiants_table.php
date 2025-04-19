<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Vérifier et ajouter chaque colonne si elle n'existe pas
            if (!Schema::hasColumn('etudiants', 'email')) {
                $table->string('email')->nullable()->after('matricule');
            }
            
            if (!Schema::hasColumn('etudiants', 'origine')) {
                $table->string('origine', 100)->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('etudiants', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('origine');
                $table->foreign('parent_id')
                      ->references('id')
                      ->on('parents')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Ne supprimer que si la colonne existe
            if (Schema::hasColumn('etudiants', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            // Optionnel : supprimer les autres colonnes
            // $table->dropColumn(['email', 'origine']);
        });
    }
};
