<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Classroom;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetardsController extends Controller
{
    use HasFactory;
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
        'surveillant_id' => 'required|exists:surveillant,id',
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
    

    
    // ✅ Personnalisée : retards d’un étudiant
    public function getByEtudiant($etudiant_id)
    {
        $retards = Retard::where('etudiant_id', $etudiant_id)
                    ->with('etudiant')
                    ->get();

        return response()->json($retards);
    }

    // ✅ Personnalisée : retards d’un étudiant entre deux dates
    public function getByDateRange($etudiant_id, $date_debut, $date_fin)
    {
        $retards = Retard::where('etudiant_id', $etudiant_id)
                    ->whereBetween('date', [$date_debut, $date_fin])
                    ->with('etudiant')
                    ->get();

        return response()->json($retards);


    }
}
