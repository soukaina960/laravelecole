<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Http\Requests\StoreFiliereRequest;
use App\Http\Requests\UpdateFiliereRequest;
use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Etudiant;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Récupérer toutes les filières avec un tri par nom
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
            // En cas d'erreur, retour d'une réponse avec code 500
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filières',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Valider les données envoyées dans la requête
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'code' => 'required|string|max:100|unique:filieres,code',
                'description' => 'nullable|string|max:500'
            ]);
            
            // Créer la filière à partir des données validées
            $filiere = Filiere::create([
                'nom' => $validated['nom'],
                'code' => $validated['code'],
                'description' => $validated['description']
            ]);
            
            // Retourner une réponse JSON avec le succès
            return response()->json([
                'success' => true,
                'message' => 'Filière créée avec succès',
                'data' => $filiere
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si la validation échoue, retourner un message d'erreur spécifique
            return response()->json([
                'success' => false,
                'message' => 'Les données envoyées ne sont pas valides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Si une autre exception survient, retourner un message d'erreur générique
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la filière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            // Récupérer la filière avec ses classes associées
            $filiere = Filiere::with(['classrooms:id,nom,filiere_id'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Détails de la filière récupérés avec succès',
                'data' => $filiere
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, retour d'une réponse avec code 404
            return response()->json([
                'success' => false,
                'message' => 'Filière non trouvée',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    public function update(Request $request, $id)
{
    try {
        // Récupérer la filière à mettre à jour
        $filiere = Filiere::findOrFail($id);
        
        // Valider les données de la requête
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:filieres,code,' . $filiere->id, // Permet de vérifier l'unicité sauf pour la filière actuelle
            'description' => 'nullable|string|max:500'
        ]);

        // Mettre à jour la filière avec les données validées
        $filiere->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Filière mise à jour avec succès',
            'data' => $filiere
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Si la validation échoue, retourner un message d'erreur spécifique
        return response()->json([
            'success' => false,
            'message' => 'Les données envoyées ne sont pas valides',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        // En cas d'erreur générale, renvoyer une erreur interne avec code 500
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour de la filière',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

    public function destroy($id)
    {
        try {
            // Récupérer la filière à supprimer
            $filiere = Filiere::findOrFail($id);
            
            // Vérifier si la filière contient des classes avant de la supprimer
            if ($filiere->classrooms()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une filière contenant des classes'
                ], 422);
            }
            
            // Supprimer la filière
            $filiere->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Filière supprimée avec succès'
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, retour d'une réponse avec code 500
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
            // Récupérer les classes associées à la filière
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
            // En cas d'erreur, retour d'une réponse avec code 404
            return response()->json([
                'success' => false,
                'message' => 'Filière non trouvée ou erreur de récupération',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    // Méthodes supplémentaires
    public function getFilieres()
    {
        $filieres = Filiere::all(); // Récupérer toutes les filières
        return response()->json($filieres);
    }

    public function getClassesLycee()
    {
        $classes = Classe::where('niveau', 'lycee')->get(); // Récupérer les classes au niveau lycée
        return response()->json($classes);
    }

    public function getMatieres($classe)
    {
        $matieres = Matiere::where('classe_id', $classe)->get(); // Récupérer les matières pour une classe
        return response()->json($matieres);
    }

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
