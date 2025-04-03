<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Vérifiez d'abord si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('etudiants', 'email')) {
                $table->string('email')->nullable()->after('matricule');
            }
            
            if (!Schema::hasColumn('etudiants', 'origine')) {
                $table->string('origine')->nullable()->after('email');
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
            // Supprimez seulement si la colonne existe
            if (Schema::hasColumn('etudiants', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            // Optionnel : supprimez les autres colonnes si nécessaire
            // $table->dropColumn(['email', 'origine']);
        });
    }
};
