<?php
// app/Http/Controllers/EtudiantController.php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Professeur;
use App\Models\Utilisateur;


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
    
    
    
    
}
    

