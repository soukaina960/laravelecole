<?php
namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        try {
            $matieres = Matiere::all();
            return response()->json([
                'success' => true,
                'message' => 'Liste des matières récupérées avec succès',
                'data' => $matieres
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des matières',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
            ]);
            
            $matiere = Matiere::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Matière créée avec succès',
                'data' => $matiere
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la matière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $matiere = Matiere::findOrFail($id);
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
            ]);
            
            $matiere->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Matière mise à jour avec succès',
                'data' => $matiere
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la matière',
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
            $matiere = Matiere::findOrFail($id);
            $matiere->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Matière supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la matière',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
