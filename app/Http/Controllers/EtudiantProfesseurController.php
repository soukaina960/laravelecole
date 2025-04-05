<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtudiantProfesseurController extends Controller
{
    // Méthode pour récupérer les étudiants-professeurs d'une classe
    public function getEtudiantsProfesseurs($classeId)
    {
        $etudiantsProfesseurs = DB::table('etudiant_professeur')
            ->join('etudiants', 'etudiants.id', '=', 'etudiant_professeur.etudiant_id')
            ->join('professeurs', 'professeurs.id', '=', 'etudiant_professeur.professeur_id')
            ->where('etudiants.classe_id', $classeId)
            ->get(['etudiants.id as etudiant_id', 'professeurs.id as professeur_id']);
        
        return response()->json(['data' => $etudiantsProfesseurs]);
    }
}
