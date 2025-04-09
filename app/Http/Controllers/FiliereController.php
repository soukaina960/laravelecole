<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Http\Requests\StoreFiliereRequest;
use App\Http\Requests\UpdateFiliereRequest;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    /**
     * Display a listing of all filieres
     */
    public function index()
    {
        try {
            $filieres = Filiere::query()
                ->select('id', 'nom', 'code', 'description', 'created_at', 'updated_at')
                ->orderBy('nom')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Liste des filières récupérée avec succès',
                'data' => $filieres,
                'count' => $filieres->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filières',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created filiere
     */
    public function store(StoreFiliereRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $filiere = Filiere::create([
                'nom' => $validated['nom'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Filière créée avec succès',
                'data' => $filiere
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la filière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified filiere
     */
    public function show($id)
    {
        try {
            $filiere = Filiere::select('id', 'nom', 'code', 'description', 'created_at', 'updated_at')
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Détails de la filière récupérés avec succès',
                'data' => $filiere
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filière non trouvée',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Update the specified filiere
     */
    public function update(UpdateFiliereRequest $request, $id)
    {
        try {
            $filiere = Filiere::findOrFail($id);
            $validated = $request->validated();
            
            $filiere->update([
                'nom' => $validated['nom'] ?? $filiere->nom,
                'code' => $validated['code'] ?? $filiere->code,
                'description' => $validated['description'] ?? $filiere->description
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Filière mise à jour avec succès',
                'data' => $filiere
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la filière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified filiere
     */
    public function destroy($id)
    {
        try {
            $filiere = Filiere::findOrFail($id);
            
            // Vérifier si la filière a des classes avant suppression
            if ($filiere->classrooms()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une filière contenant des classes'
                ], 422);
            }
            
            $filiere->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Filière supprimée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la filière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get classrooms for a specific filiere
     */
    public function getClasses($filiereId)
    {
        try {
            $classrooms = Filiere::findOrFail($filiereId)
                ->classrooms()
                ->select('id', 'name as nom', 'filiere_id', 'niveau', 'capacite')
                ->orderBy('niveau')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Classes de la filière récupérées avec succès',
                'data' => $classrooms,
                'count' => $classrooms->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filière non trouvée ou erreur de récupération',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }
}