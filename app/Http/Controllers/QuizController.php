<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Matiere;
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
}
