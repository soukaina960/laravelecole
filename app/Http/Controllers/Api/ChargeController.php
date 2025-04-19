<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;
use Carbon\Carbon;  // Assure-toi d'importer Carbon pour manipuler les dates

class ChargeController extends Controller
{
    // Lister toutes les charges
    public function index(Request $request)
    {
        $mois = $request->query('mois');  // Récupérer le mois depuis la requête
        $annee = $request->query('annee'); // Récupérer l'année depuis la requête

        // Récupérer les charges pour le mois et l'année spécifiés, ou toutes si non spécifiés
        $charges = Charge::when($mois, function ($query) use ($mois) {
            return $query->where('mois', $mois);
        })
        ->when($annee, function ($query) use ($annee) {
            return $query->where('annee', $annee);
        })
        ->get();

        return response()->json([
            'success' => true,
            'data' => $charges
        ]);
    }

    // Ajouter une charge
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
        ]);

        // Utiliser la date actuelle pour remplir automatiquement 'mois' et 'annee'
        $currentMonth = Carbon::now()->month;  // Mois actuel
        $currentYear = Carbon::now()->year;   // Année actuelle

        // Ajouter une charge avec les champs mois et année automatiquement définis
        $charge = Charge::create([
            'description' => $validated['description'],
            'montant' => $validated['montant'],
            'mois' => $currentMonth,
            'annee' => $currentYear,
        ]);

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
