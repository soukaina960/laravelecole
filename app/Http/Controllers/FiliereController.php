<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Http\Requests\StoreFiliereRequest;
use App\Http\Requests\UpdateFiliereRequest;
use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Matiere;


class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $filieres = Filiere::query()
                ->select('id', 'nom', 'code', 'description', 'created_at')
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
     * Store a newly created resource in storage.
     */
    public function store(StoreFiliereRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $filiere = Filiere::create($validated);
            
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $filiere = Filiere::with(['classrooms:id,nom,filiere_id'])
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
     * Update the specified resource in storage.
     */
    public function update(UpdateFiliereRequest $request, $id)
    {
        try {
            $filiere = Filiere::findOrFail($id);
            $validated = $request->validated();
            
            $filiere->update($validated);
            
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $filiere = Filiere::findOrFail($id);
            
            // Check if filiere has classrooms before deletion
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
                ->select('id', 'nom', 'filiere_id', 'niveau')
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
    public function getFilieres()
    {
        $filieres = Filiere::all(); // Assure-toi que la table 'filieres' existe et contient des données
        return response()->json($filieres);
    }

    // Récupérer les classes disponibles pour le lycée
    public function getClassesLycee()
    {
        $classes = Classe::where('niveau', 'lycee')->get(); // Assure-toi que la table 'classes' contient un champ 'niveau'
        return response()->json($classes);
    }

    // Récupérer les matières pour une classe donnée
    public function getMatieres($classe)
    {
        $matieres = Matiere::where('classe_id', $classe)->get(); // Assure-toi que chaque matière est liée à une classe
        return response()->json($matieres);
    }

    // Récupérer les étudiants d'une classe et, si nécessaire, d'une filière spécifique
    public function getEtudiants($classe, $filiere = null)
    {
        $query = Etudiant::where('classe_id', $classe); // Filtrer par classe

        if ($filiere) {
            $query->where('filiere_id', $filiere); // Filtrer par filière si elle est donnée
        }

        $etudiants = $query->get();
        return response()->json($etudiants);
    }
}