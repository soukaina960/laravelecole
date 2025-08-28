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
    private function nettoyerUtf8($donnees)
{
    if (is_string($donnees)) {
        return mb_convert_encoding($donnees, 'UTF-8', 'UTF-8');
    } elseif (is_array($donnees)) {
        return array_map([$this, 'nettoyerUtf8'], $donnees);
    } elseif (is_object($donnees)) {
        foreach ($donnees as $cle => $valeur) {
            $donnees->$cle = $this->nettoyerUtf8($valeur);
        }
    }
    return $donnees;
}

 public function index()
{
    $retards = Retard::with('etudiant')->get();

    // Nettoyage des caractères mal encodés
    $retardsUtf8 = $this->nettoyerUtf8($retards->toArray());

    return response()->json($retardsUtf8);
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
    $retard = Retard::findOrFail($id);

    // On convertit en tableau puis on nettoie récursivement l'encodage
    $data = $this->utf8Clean($retard->toArray());

    return response()->json($data);
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
    private function utf8Clean($data)
{
    if (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    } elseif (is_array($data)) {
        return array_map([$this, 'utf8Clean'], $data);
    } elseif (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = $this->utf8Clean($value);
        }
        return $data;
    }
    return $data;
}

  public function getRetardsByParentId($parentId)
{
    if (!$parentId) {
        return response()->json(['message' => 'parent_id manquant'], 400);
    }

    $retards = Retard::whereHas('etudiant', function ($query) use ($parentId) {
        $query->where('parent_id', $parentId);
    })->with(['etudiant', 'classroom', 'matiere', 'professeur'])->get();

    // Nettoyage UTF-8
    $data = $this->utf8Clean($retards->toArray());

    return response()->json($data); // Même s'il est vide, pas d'erreur côté front
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
