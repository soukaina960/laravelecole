<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use Illuminate\Http\Request;

class RetardsController extends Controller
{
    public function index()
    {
        return response()->json(Retard::with('etudiant')->get());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'etudiant_id' => 'required|exists:etudiants,id',
        'date' => 'required|date',
        'heure' => 'required|date_format:H:i',
        'professeur_id' => 'required|exists:professeurs,id',
        'class_id' => 'required|exists:classrooms,id',
        'matiere_id' => 'required|exists:matieres,id',
    ]);

    $retard = Retard::create($validated);

    return response()->json($retard, 201);
}

    public function show($id)
    {
        return response()->json(Retard::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $retard = Retard::findOrFail($id);
        $retard->update($request->all());

        return response()->json($retard);
    }

    public function destroy($id)
    {
        Retard::destroy($id);
        return response()->json(['message' => 'Retard supprimé']);
    }
    public function getRetardsByParentId($parentId)
    {
        // Vérifie si le parent_id est présent dans l'URL
        if (!$parentId) {
            return response()->json(['message' => 'parent_id manquant'], 400);
        }

        // Récupère les absences où le parent_id correspond
        $absences = Retard::whereHas('etudiant', function ($query) use ($parentId) {
        $query->where('parent_id', $parentId);
    })->with(['etudiant', 'classroom', 'matiere', 'professeur']) 
    ->get();
        // Si aucune absence n'est trouvée
        if ($absences->isEmpty()) {
            return response()->json(['message' => 'Aucune absence trouvée pour ce parent_id'], 404);
        }

        // Retourne les absences sous forme de JSON
        return response()->json($absences);
    }
}
