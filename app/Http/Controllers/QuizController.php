<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Matiere;
 use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    // Afficher tous les quiz
   // Dans votre contrôleur QuizController.php
// QuizController.php
public function index(Request $request)
{
    return Quiz::when($request->class_id, function($query, $class_id) {
        $query->where('class_id', $class_id);
    })->get();
}

    // Enregistrer un nouveau quiz
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classrooms,id',
            'matiere_id' => 'required|exists:matieres,id',
            'question_text' => 'required|string',
            'answer' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::create($request->all());

        return response()->json($quiz, 201);
    }

    // Voir un seul quiz
    public function show($id)
    {
        $quiz = Quiz::findOrFail($id);
        return response()->json($quiz);
    }

    // Supprimer un quiz
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
        return response()->json(['message' => 'Quiz supprimé avec succès']);
    }
 public function update(Request $request, $id)
{
    // 1. Validation des données
    $validator = Validator::make($request->all(), [
        'class_id' => 'required|exists:classrooms,id',
        'matiere_id' => 'required|exists:matieres,id',
        'question_text' => 'required|string|max:500',
        'answer' => 'required|boolean',
        'description' => 'nullable|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Recherche du quiz à mettre à jour
    $quiz = Quiz::find($id);

    if (!$quiz) {
        return response()->json([
            'status' => 'error',
            'message' => 'Quiz not found'
        ], 404);
    }

    // 3. Mise à jour du quiz (sans vérification de propriétaire)
    try {
        $validatedData = $validator->validated();

        $quiz->update([
            'class_id' => $validatedData['class_id'],
            'matiere_id' => $validatedData['matiere_id'],
            'question_text' => $validatedData['question_text'],
            'answer' => $validatedData['answer'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz updated successfully',
            'data' => $quiz
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update quiz',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
