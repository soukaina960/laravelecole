<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Charge;
use PDF;



class RapportController extends Controller
{
    public function exportPdf()
    {
        $totalEtudiants = Etudiant::count();
        $totalProfs = Professeur::count();

        $montantEtudiants = Etudiant::sum('montant_a_payer');
        $montantProfs = Professeur::sum('montant');
        $totalCharges = Charge::sum('montant');

        $resultatFinal = $montantEtudiants - ($montantProfs + $totalCharges);

        $data = [
            'totalEtudiants' => $totalEtudiants,
            'totalProfs' => $totalProfs,
            'montantEtudiants' => $montantEtudiants,
            'montantProfs' => $montantProfs,
            'totalCharges' => $totalCharges,
            'resultatFinal' => $resultatFinal,
        ];

        $pdf = Pdf::loadView('rapport.pdf', $data);
        return $pdf->stream('rapport-ecole.pdf');
    }
}
