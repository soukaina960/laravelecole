<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements_mensuels', function (Blueprint $table) {
            // Supprimer l'ancienne colonne 'mois'
            $table->dropColumn('mois');

            // Ajouter les nouvelles colonnes
            $table->char('mois', 2)->after('etudiant_id');
        });
    }

    public function down(): void
    {
        Schema::table('paiements_mensuels', function (Blueprint $table) {
            // Restaurer l'ancienne colonne
            $table->date('mois')->after('etudiant_id');

            // Supprimer les nouvelles colonnes
            $table->dropColumn('mois');
        });
    }
};
