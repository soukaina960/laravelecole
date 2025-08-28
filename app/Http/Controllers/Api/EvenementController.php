<?php

namespace App\Http\Controllers\API;

use App\Models\Evenement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Etudiant;

class EvenementController extends Controller
{
    // Afficher la liste des événements
   public function index(Request $request)
{
    $query = Evenement::with('classe');

    if ($request->has('class_id')) {
        $query->where('class_id', $request->class_id);
    }

    return $query->get();
}

    

    // Créer un nouvel événement
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'lieu' => 'required|string|max:255',
            'class_id' => 'nullable|exists:classrooms,id',
        ]);
    
        return Evenement::create($data);
    }
    
    // Afficher un événement spécifique
    public function show($id)
    {
        $evenement = Evenement::findOrFail($id);
        return response()->json($evenement);
    }

    // Mettre à jour un événement
    public function update(Request $request, $id)
{
    $evenement = Evenement::findOrFail($id);

    $data = $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'lieu' => 'required|string|max:255',
        'class_id' => 'nullable|exists:classrooms,id',
    ]);

    $evenement->update($data);

    return $evenement;
}


    // Supprimer un événement
    public function destroy($id)
    {
        $evenement = Evenement::findOrFail($id);
        $evenement->delete();

        return response()->json(null, 204);
    }
  public function getEvenementsByParentId($parentId)
{
    // Récupérer tous les étudiants liés à ce parent
    $etudiants = Etudiant::where('parent_id', $parentId)->get();

    if ($etudiants->isEmpty()) {
        return response()->json(['message' => 'Aucun étudiant trouvé pour ce parent'], 404);
    }

    // Extraire tous les IDs de classe
    $classeIds = $etudiants->pluck('classe_id')->unique();

    // Récupérer tous les événements pour ces classes
    $evenements = Evenement::whereIn('class_id', $classeIds)->get();

    return response()->json($evenements);
}

  
}

