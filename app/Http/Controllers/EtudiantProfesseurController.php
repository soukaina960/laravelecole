<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtudiantProfesseurController extends Controller
{
    // Méthode pour récupérer les étudiants-professeurs d'une classe
    public function getEtudiantsProfesseurs($classeId)
    {
        // Vérifier que la classe existe
        $classe = DB::table('classes')->where('id', $classeId)->first();
        if (!$classe) {
            return response()->json(['message' => 'Classe non trouvée.'], 404);
        }

        // Récupérer les étudiants-professeurs
        $etudiantsProfesseurs = DB::table('etudiant_professeur')
            ->join('etudiants', 'etudiants.id', '=', 'etudiant_professeur.etudiant_id')
            ->join('professeurs', 'professeurs.id', '=', 'etudiant_professeur.professeur_id')
            ->where('etudiants.classe_id', $classeId)
            ->get(['etudiants.id as etudiant_id', 'professeurs.id as professeur_id', 'etudiants.nom as etudiant_nom', 'professeurs.nom as professeur_nom']);

        // Vérifier si des résultats ont été trouvés
        if ($etudiantsProfesseurs->isEmpty()) {
            return response()->json(['message' => 'Aucun étudiant-professeur trouvé pour cette classe.'], 404);
        }

        // Retourner les résultats
        return response()->json(['data' => $etudiantsProfesseurs]);
    }
  

    // Méthode pour enregistrer les absences
    public function enregistrerAbsences(Request $request)
    {
        // Validation
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'etudiants_absents' => 'required|array', // Assure-toi que c'est un tableau
            'etudiants_absents.*' => 'exists:etudiants,id', // Vérifie que chaque étudiant existe
        ]);

        // Enregistrement des absences
        foreach ($request->etudiants_absents as $etudiantId) {
            DB::table('absences')->insert([
                'etudiant_id' => $etudiantId,
                'classe_id' => $request->classe_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Absences enregistrées avec succès']);
    }
}


