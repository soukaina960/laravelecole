<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class IncidentController extends Controller
{
  

    public function index()
    {
        try {
            $incidents = Incident::with('etudiant')->get();
            return response()->json($incidents, 200, [], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Error fetching incidents', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'etudiant_id' => 'required|exists:etudiants,id',
                'description' => 'required|string',
                'date' => 'required|date',
                'professeur_id' => 'required|exists:professeurs,id',
                'class_id' => 'required|exists:classrooms,id',
                'matiere_id' => 'required|exists:matieres,id',
                'surveillant_id' => 'required|exists:surveillant,id',
            ]);

            // Nettoyage des données UTF-8
            $validated['description'] = mb_convert_encoding($validated['description'], 'UTF-8', 'UTF-8');

            $incident = Incident::create($validated);

            return response()->json($incident, 201, ['Content-Type' => 'application/json;charset=UTF-8'], JSON_UNESCAPED_UNICODE);
        } catch (ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            return response()->json($e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error creating incident', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function show($id)
    {
        try {
            $incident = Incident::findOrFail($id);
            return response()->json($incident, 200, [], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Error fetching incident', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Incident not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'etudiant_id' => 'sometimes|exists:etudiants,id',
                'description' => 'sometimes|string',
                'date' => 'sometimes|date',
                'professeur_id' => 'sometimes|exists:professeurs,id',
                'class_id' => 'sometimes|exists:classrooms,id',
                'matiere_id' => 'sometimes|exists:matieres,id',
                'surveillant_id' => 'sometimes|exists:surveillant,id',
            ]);

            // Nettoyage des données UTF-8
            if (isset($validated['description'])) {
                $validated['description'] = mb_convert_encoding($validated['description'], 'UTF-8', 'UTF-8');
            }

            $incident = Incident::findOrFail($id);
            $incident->update($validated);

            return response()->json($incident, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error updating incident', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error updating incident'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Incident::destroy($id);
            return response()->json(['message' => 'Incident supprimé']);
        } catch (\Exception $e) {
            Log::error('Error deleting incident', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error deleting incident'], 500);
        }
    }

    public function getByEtudiant($etudiant_id)
    {
        try {
            $incidents = Incident::where('etudiant_id', $etudiant_id)
                        ->with('etudiant')
                        ->get();

            return response()->json($incidents, 200, [], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Error fetching student incidents', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error fetching incidents'], 500);
        }
    }

    public function getByDateRange($etudiant_id, $date_debut, $date_fin)
    {
        try {
            $incidents = Incident::where('etudiant_id', $etudiant_id)
                        ->whereBetween('date', [$date_debut, $date_fin])
                        ->with('etudiant')
                        ->get();

            return response()->json($incidents, 200, [], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Error fetching date range incidents', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error fetching incidents'], 500);
        }
    }

   public function getIncidentsByParentId($parentId)
{
    try {
        // Vérification si l'ID du parent est fourni
        if (!$parentId) {
            return response()->json([
                'success' => false,
                'message' => 'parent_id manquant'
            ], 400);
        }

        // Requête : récupérer les incidents liés aux étudiants de ce parent
        $incidents = Incident::whereHas('etudiant', function ($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            })
            ->with(['etudiant', 'classroom', 'matiere', 'professeur'])
            ->get();

        // Si aucun incident trouvé
        if ($incidents->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Aucun incident trouvé pour ce parent_id'
            ], 200);
        }

        // Retourner les incidents trouvés
        return response()->json([
            'success' => true,
            'data' => $incidents
        ], 200, [], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);

    } catch (\Exception $e) {
        // En cas d'erreur serveur
        Log::error('Erreur lors de la récupération des incidents du parent', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des incidents'
        ], 500);
    }
}

}