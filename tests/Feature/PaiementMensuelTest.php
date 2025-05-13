<?php

namespace Tests\Feature;

use App\Models\Etudiant;
use App\Models\PaiementMensuel;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaiementMensuelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de la réinitialisation des paiements au début du mois
     */
    public function test_reset_paiements_and_create_new_for_current_month()
    {
        // Créer un étudiant
        $etudiant = Etudiant::factory()->create();

        // Créer un paiement pour le mois d'avril (mois précédent)
        PaiementMensuel::create([
            'etudiant_id' => $etudiant->id,
            'mois' => '04', // Avril
            'date_paiement' => '2025-04-25',
            'est_paye' => true,
        ]);

        // Simuler le 1er mai 2025
        Carbon::setTestNow(Carbon::create(2025, 5, 1));

        // Faire une requête POST pour créer un paiement pour le mois de mai
        $response = $this->postJson('/api/paiements', [
            'etudiant_id' => $etudiant->id,
        ]);

        // Vérifier que la réponse est correcte
        $response->assertStatus(201);

        // Vérifier que le paiement du mois précédent (avril) a été réinitialisé à non payé
        $this->assertDatabaseHas('paiements_mensuels', [
            'etudiant_id' => $etudiant->id,
            'mois' => '04',  // Avril
            'est_paye' => false, // Réinitialisé à non payé
        ]);

        // Vérifier que le paiement pour le mois actuel (mai) a bien été créé
        $this->assertDatabaseHas('paiements_mensuels', [
            'etudiant_id' => $etudiant->id,
            'mois' => '05', // Mai
            'est_paye' => true, // Payé
        ]);
    }
}
