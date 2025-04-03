<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
    /**
     * Affiche l'email du parent d'un étudiant spécifique
     * 
     * @param int $etudiant_id L'ID de l'étudiant
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentEmail($etudiant_id)
    {
        try {
            // 1. Récupérer l'étudiant avec son parent
            $etudiant = Etudiant::with(['parent' => function($query) {
                $query->select('id', 'email'); // On ne sélectionne que l'email du parent
            }])
            ->select('id', 'parent_id') // On ne sélectionne que les champs nécessaires
            ->findOrFail($etudiant_id);

            // 2. Vérifier si un parent est associé
            if (!$etudiant->parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun parent associé à cet étudiant'
                ], 404);
            }

            // 3. Retourner la réponse
            return response()->json([
                'success' => true,
                'etudiant_id' => $etudiant->id,
                'parent_email' => $etudiant->parent->email,
                'parent_info' => [
                    'nom' => $etudiant->parent->nom,
                    'prenom' => $etudiant->parent->prenom
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur dans ParentEmailController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }
}