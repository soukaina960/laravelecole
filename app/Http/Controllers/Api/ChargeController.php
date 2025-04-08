<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    // Lister toutes les charges
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Charge::all()
        ]);
    }

    // Ajouter une charge
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
        ]);

        $charge = Charge::create($validated);

        return response()->json([
            'success' => true,
            'data' => $charge
        ], 201);
    }

    // Afficher une charge spécifique
    public function show(Charge $charge)
    {
        return response()->json([
            'success' => true,
            'data' => $charge
        ]);
    }

    // Modifier une charge
    public function update(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'description' => 'sometimes|string|max:255',
            'montant' => 'sometimes|numeric|min:0',
        ]);

        $charge->update($validated);

        return response()->json([
            'success' => true,
            'data' => $charge
        ]);
    }

    // Supprimer une charge
    public function destroy(Charge $charge)
    {
        $charge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Charge supprimée avec succès.'
        ]);
    }
}
