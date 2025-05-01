<?php
// app/Http/Controllers/EtudiantController.php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Professeur;
use App\Models\Utilisateur;
use App\Models\Matiere;


class EtudiantController extends Controller
{
    // Appliquez le middleware 'auth:api' dans le constructeur
   
    /**
     * Récupère les informations d'un étudiant spécifique (pour admin/profs)
     */
    public function getCurrentStudentInfo()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        $etudiant = Etudiant::with('classe')
            ->where('utilisateur_id', $user->id)
            ->first();

        if (!$etudiant) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        return response()->json($etudiant);
    }

    /**
     * Récupère les informations d'un étudiant spécifique (pour admin/profs)
     */


    /**
     * Récupère les infos d'un étudiant par son ID
     */
    public function show($id)
    {
        $etudiant = Etudiant::with('utilisateur')->find($id);
        if ($etudiant) {
            return response()->json($etudiant);
        }
        return response()->json(['message' => 'Étudiant non trouvé'], 404);
    }

    public function classroom()
{
    return $this->belongsTo(Classroom::class, 'class_id');
}
public function getMatieresPourProfesseurEtClasse($professeurId, $classeId)
{
    // Récupérer les matières pour un professeur et une classe via la table pivot
    $matieres = DB::table('prof_matiere_classe')
                  ->join('matieres', 'matieres.id', '=', 'prof_matiere_classe.matiere_id')
                  ->where('prof_matiere_classe.professeur_id', $professeurId)
                  ->where('prof_matiere_classe.classe_id', $classeId)
                  ->select('matieres.*')
                  ->get();
    
    return response()->json($matieres);
}


// Récupérer les classes d'une matière
public function getClasses($matiereId)
{
    $classes = classroom::where('matiere_id', $matiereId)->get();
    return response()->json($classes);
}

// Récupérer les quiz pour une matière et une classe
public function getQuizzes(Request $request)
{
    $quizzes = Quiz::where('matiere_id', $request->matiere_id)
                   ->where('classe_id', $request->class_id)
                   ->get();
    return response()->json($quizzes);
}

    
    
    
}
    

