<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Classroom;
use App\Models\Charge;
use Illuminate\Http\JsonResponse;
use App\Models\SalaireMensuel;
use App\Models\PaiementMensuel;
use Carbon\Carbon;
use App\Models\StatistiqueMensuelle;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $moisActuel = Carbon::now()->month;
        $anneeActuelle = Carbon::now()->year;

        // Récupérer les statistiques
        $nombreEtudiants = Etudiant::count();
        $nombreProfesseurs = Professeur::count();
        $nombreClasses = Classroom::count();

        // Calculer les revenus
        $revenus = PaiementMensuel::where('mois', $moisActuel)
            ->whereYear('date_paiement', $anneeActuelle)
            ->where('est_paye', 1)
            ->with('etudiant') // relation avec le modèle Etudiant
            ->get()
            ->sum(function ($paiement) {
                return $paiement->etudiant->montant_a_payer ?? 0;
            });

        // Calculer les salaires et les charges
        $salaires = SalaireMensuel::where('mois', $moisActuel)
            ->where('annee', $anneeActuelle)
            ->sum('salaire');

        $charges = Charge::where('mois', $moisActuel)
            ->where('annee', $anneeActuelle)
            ->sum('montant');

        $depenses = $salaires + $charges;
        $reste = $revenus - $depenses;

        // Nombre d'étudiants sans classe
        $alertes = Etudiant::whereNull('classe_id')->count();

        // Mettre à jour ou créer une nouvelle entrée dans StatistiqueMensuelle
        StatistiqueMensuelle::updateOrCreate(
            ['mois' => $moisActuel, 'annee' => $anneeActuelle],
            [
                'etudiants' => $nombreEtudiants,
                'professeurs' => $nombreProfesseurs,
                'classes' => $nombreClasses,
                'revenus' => $revenus,
                'depenses' => $depenses,
                'salaires' => $salaires,
                'charges' => $charges,
                'alertes' => $alertes,
                'reste' => $reste,
            ]
        );

        // Retourner les données sous forme de réponse JSON
        return response()->json([
            'etudiants' => $nombreEtudiants,
            'professeurs' => $nombreProfesseurs,
            'classes' => $nombreClasses,
            'revenus' => $revenus,
            'depenses' => $depenses,
            'profs' => $salaires,
            'charges' => $charges,
            'alertes' => $alertes,
            'reste' => $reste,
        ]);
    }
}
