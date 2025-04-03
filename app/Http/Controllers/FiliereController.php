<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    /**
     * Récupère toutes les filièresggffhhhvcgcgffffdddd
     */
    public function index()
    {

        try {
            $filieres = Filiere::select('id', 'nom', 'code')->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Liste des filières récupérée avec succès',
                'data' => $filieres
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des filières',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les classes d'une filière
     */
    public function getClasses($filiereId)
    {
        try {
            $filiere = Filiere::with(['classes' => function($query) {
                $query->select('id', 'nom', 'filiere_id');
            }])->findOrFail($filiereId);
            
            return response()->json([
                'status' => true,
                'message' => 'Classes de la filière récupérées avec succès',
                'data' => $filiere->classes
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Filière non trouvée ou erreur de récupération',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}