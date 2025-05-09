<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'parent_id' => 'required|exists:parents,id',
        ]);

        $reclamation = Reclamation::create($validated);

        return response()->json([
            'message' => 'Réclamation envoyée avec succès.',
            'reclamation' => $reclamation
        ], 201);
    }

    public function index()
    {
        // Retourner toutes les réclamations
        $reclamations = Reclamation::with('parent')->get();
        return response()->json($reclamations);
    }
    public function update(Request $request, $id)
{
    $reclamation = Reclamation::findOrFail($id);
    $reclamation->statut = $request->statut;
    $reclamation->save();

    return response()->json(['message' => 'Statut mis à jour avec succès']);
}
public function reclamationsParParent($id)
{
    $reclamations = Reclamation::where('parent_id', $id)->orderByDesc('created_at')->get();
    return response()->json(['reclamations' => $reclamations]);
}

public function annuler($id)
{
    $reclamation = Reclamation::findOrFail($id);

    if ($reclamation->statut !== 'en attente') {
        return response()->json(['message' => 'Impossible d’annuler cette réclamation.'], 400);
    }

    $reclamation->statut = 'annulée';
    $reclamation->save();

    return response()->json(['message' => 'Réclamation annulée avec succès.']);
}
public function destroy($id)
{
    // Chercher la réclamation par ID
    $reclamation = Reclamation::find($id);

    // Vérifier si elle existe
    if (!$reclamation) {
        return response()->json(['message' => 'Réclamation non trouvée.'], 404);
    }

    // Optionnel : empêcher de supprimer si le statut n'est pas "en attente"
    if ($reclamation->statut !== 'en attente') {
        return response()->json(['message' => 'Seules les réclamations en attente peuvent être supprimées.'], 403);
    }

    // Supprimer la réclamation
    $reclamation->delete();

    // Retourner une réponse
    return response()->json(['message' => 'Réclamation supprimée avec succès.']);
}

}
