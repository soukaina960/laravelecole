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

}
