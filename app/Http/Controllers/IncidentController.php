<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        return response()->json(Incident::with('etudiant')->get());
    }

    // Store method with error handling for validation failures
public function store(Request $request)
{
    try {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $incident = Incident::create($request->all());

        return response()->json($incident, 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Log the validation errors
        Log::error('Validation failed', $e->errors());
        return response()->json($e->errors(), 422);
    }
}


    public function show($id)
    {
        return response()->json(Incident::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);
        $incident->update($request->all());

        return response()->json($incident);
    }

    public function destroy($id)
    {
        Incident::destroy($id);
        return response()->json(['message' => 'Incident supprimé']);
    }
    // 🔎 Get all incidents for a specific student
public function getByEtudiant($etudiant_id)
{
    $incidents = Incident::where('etudiant_id', $etudiant_id)
                ->with('etudiant')
                ->get();

    return response()->json($incidents);
}

// 📅 Get incidents for a student in a date range
public function getByDateRange($etudiant_id, $date_debut, $date_fin)
{
    $incidents = Incident::where('etudiant_id', $etudiant_id)
                ->whereBetween('date', [$date_debut, $date_fin])
                ->with('etudiant')
                ->get();

    return response()->json($incidents);
}

}
