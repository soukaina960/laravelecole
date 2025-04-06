<?php

namespace App\Http\Controllers;

use App\Models\EmploiTemps;
use Illuminate\Http\Request;

class EmploiTempsController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => EmploiTemps::with(['classe', 'professeur'])->get()
        ]);
    }

    // Créer un nouvel emploi du temps
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'jour' => 'required|string|max:10',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'matiere' => 'required|string|max:255',
            'professeur_id' => 'required|exists:professeurs,id',
            'salle' => 'required|string|max:20'
        ]);

        $emploi = EmploiTemps::create($validated);

        return response()->json([
            'success' => true,
            'data' => $emploi
        ], 201);
    }

    // Mettre à jour un emploi du temps
    public function update(Request $request, EmploiTemps $emploiTemps)
    {
        $validated = $request->validate([
            'classe_id' => 'sometimes|exists:classrooms,id',
            'jour' => 'sometimes|string|max:10',
            'heure_debut' => 'sometimes|date_format:H:i',
            'heure_fin' => 'sometimes|date_format:H:i|after:heure_debut',
            'matiere' => 'sometimes|string|max:255',
            'professeur_id' => 'sometimes|exists:professeurs,id',
            'salle' => 'sometimes|string|max:20'
        ]);

        $emploiTemps->update($validated);

        return response()->json([
            'success' => true,
            'data' => $emploiTemps
        ]);
    }

    // Supprimer un emploi du temps
    public function destroy(EmploiTemps $emploiTemps)
    {
        $emploiTemps->delete();

        return response()->json([
            'success' => true,
            'message' => 'Emploi du temps supprimé avec succès'
        ]);
    }

    // Récupérer par classe
    public function byClasse($classeId)
    {
        return response()->json([
            'success' => true,
            'data' => EmploiTemps::where('classe_id', $classeId)
                              ->with('professeur')
                              ->orderBy('jour')
                              ->orderBy('heure_debut')
                              ->get()
        ]);
    }
}