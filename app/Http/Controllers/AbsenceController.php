<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Auth;
use App\Models\Professeur;

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
 
public function getByEtudiant($id)
    {
        $absences = Absence::with(['professeur' => function($query) {
                $query->select('id', 'nom');
            }])
            ->where('etudiant_id', $id)
            ->get();

        return response()->json($absences);
    }
    public function getByStudent($etudiantId)
    {
        // Vérifie que l'étudiant existe
        $etudiant = Etudiant::find($etudiantId);
        if (!$etudiant) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        // Récupère les absences triées par date décroissante
        $absences = Absence::where('etudiant_id', $etudiantId)
                         ->orderBy('date', 'desc')
                         ->get();

        return response()->json($absences);
    }

   
    public function mesAbsences()
    {
        // Vérifie que l'utilisateur est un étudiant
        if (!Auth::user()->etudiant) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $absences = Auth::user()->etudiant->absences()
                       ->orderBy('date', 'desc')
                       ->get();

        return response()->json($absences);
    }

   
   

}
