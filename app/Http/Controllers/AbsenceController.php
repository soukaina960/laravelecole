<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index()
    {
        return response()->json(Absence::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'date' => 'required|date',
            'motif' => 'nullable|string',
            'justifiee' => 'required|boolean',
        ]);

        $absence = Absence::create($request->all());

        return response()->json($absence, 201);
    }

    public function show($id)
    {
        return response()->json(Absence::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        $absence->update($request->all());

        return response()->json($absence);
    }

    public function destroy($id)
    {
        Absence::destroy($id);
        return response()->json(['message' => 'Absence supprimée']);
    }
    // Récupérer les absences d'un étudiant
public function getByEtudiant($etudiant_id)
{
    $absences = Absence::where('etudiant_id', $etudiant_id)
                ->with('etudiant')
                ->get();

    return response()->json($absences);
}

// Récupérer les absences d'un étudiant entre deux dates
public function getByDateRange($etudiant_id, $date_debut, $date_fin)
{
    $absences = Absence::where('etudiant_id', $etudiant_id)
                ->whereBetween('date', [$date_debut, $date_fin])
                ->with('etudiant')
                ->get();

    return response()->json($absences);
}

}
