<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RetardController extends Controller
{
    /**
     * Get all retards with automatic UTF-8 conversion
     */
    public function index()
    {
        try {
            $retards = Retard::with([
                'etudiant:id,nom,prenom',
                'professeur:id,nom,prenom',
                'classroom:id,nom',
                'matiere:id,nom',
                'surveillant:id,nom,prenom'
            ])->get();

            // Convert all attributes and relations to UTF-8
            $convertedRetards = $retards->map(function ($retard) {
                return $this->convertModelToUtf8($retard);
            });

            return response()->json([
                'status' => 'success',
                'data' => $convertedRetards
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json; charset=utf-8'
            ]);

        } catch (\Exception $e) {
            Log::error('RetardController error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a new retard record
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'etudiant_id' => 'required|exists:etudiants,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'class_id' => 'required|exists:classrooms,id',
            'matiere_id' => 'required|exists:matieres,id',
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i',
            'surveillant_id' => 'required|exists:surveillants,id',
            'motif' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $retard = Retard::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'data' => $this->convertModelToUtf8($retard)
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error creating retard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create record'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific retard record
     */
    public function show($id)
    {
        try {
            $retard = Retard::with([
                'etudiant',
                'professeur',
                'classroom',
                'matiere',
                'surveillant'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $this->convertModelToUtf8($retard)
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error fetching retard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve record'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a retard record
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'etudiant_id' => 'sometimes|exists:etudiants,id',
            'professeur_id' => 'sometimes|exists:professeurs,id',
            'class_id' => 'sometimes|exists:classrooms,id',
            'matiere_id' => 'sometimes|exists:matieres,id',
            'date' => 'sometimes|date',
            'heure' => 'sometimes|date_format:H:i',
            'surveillant_id' => 'sometimes|exists:surveillants,id',
            'motif' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $retard = Retard::findOrFail($id);
            $retard->update($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $this->convertModelToUtf8($retard)
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error updating retard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update record'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a retard record
     */
    public function destroy($id)
    {
        try {
            $retard = Retard::findOrFail($id);
            $retard->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error deleting retard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete record'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Convert all model attributes and relations to UTF-8
     */
    private function convertModelToUtf8($model)
    {
        // Convert main model attributes
        $attributes = $model->getAttributes();
        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $model->$key = $this->ensureUtf8($value);
            }
        }

        // Convert loaded relations
        if ($model->relationLoaded('etudiant')) {
            $this->convertModelToUtf8($model->etudiant);
        }

        if ($model->relationLoaded('professeur')) {
            $this->convertModelToUtf8($model->professeur);
        }

        if ($model->relationLoaded('classroom')) {
            $this->convertModelToUtf8($model->classroom);
        }

        if ($model->relationLoaded('matiere')) {
            $this->convertModelToUtf8($model->matiere);
        }

        if ($model->relationLoaded('surveillant')) {
            $this->convertModelToUtf8($model->surveillant);
        }

        return $model;
    }

    /**
     * Ensure string is UTF-8 encoded
     */
    private function ensureUtf8($string)
    {
        if (!is_string($string)) {
            return $string;
        }

        // Check if already valid UTF-8
        if (mb_check_encoding($string, 'UTF-8')) {
            return $string;
        }

        // Try to detect encoding
        $encoding = mb_detect_encoding($string, [
            'UTF-8',
            'ISO-8859-1',
            'Windows-1252',
            'ASCII'
        ], true);

        // Convert to UTF-8 if detection succeeded
        if ($encoding !== false) {
            return mb_convert_encoding($string, 'UTF-8', $encoding);
        }

        // Fallback: remove invalid characters
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
}