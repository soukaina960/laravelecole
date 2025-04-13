<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Classe;
use App\Models\Charge;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
$moisActuel = Carbon::now()->month;
$anneeActuelle = Carbon::now()->year;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $nombreEtudiants = Etudiant::count();
        $nombreProfesseurs = Professeur::count();
        $nombreClasses = Classe::count();
    
        $revenus = Etudiant::sum('montant_a_payer');
        $depenses = Professeur::sum('total') ;
        $profs= Professeur::sum('total') ;
        $charges = Charge::where('mois', $moisActuel)->where('annee', $anneeActuelle)->sum('montant');
    
        // ➕ Mise à jour des dépenses pour inclure les charges
        $depenses += $charges;
        $reste = $revenus - ($profs + $charges);  
        $alertes = Etudiant::whereNull('classe_id')->count();
    
        return response()->json([
            'etudiants' => $nombreEtudiants,
            'professeurs' => $nombreProfesseurs,
            'classes' => $nombreClasses,
            'revenus' => $revenus,
            'depenses' => $depenses,
            'profs' => $profs,
            'charges' => $charges, // ← aussi les envoyer séparément si besoin
            'alertes' => $alertes,
            'reste' => $reste,
        ]);
        }    }
    