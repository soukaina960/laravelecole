<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatistiqueMensuelle;
use Illuminate\Http\JsonResponse;

class StatistiqueMensuelleController extends Controller
{
    /**
     * Récupérer les statistiques mensuelles pour une année donnée.
     */
    public function parAnnee(Request $request): JsonResponse
    {
        $annee = $request->input('annee', now()->year);

        $stats = StatistiqueMensuelle::where('annee', $annee)
            ->orderBy('mois')
            ->get(['mois', 'reste']); // on peut ajouter d'autres colonnes si besoin

        return response()->json($stats);
    }

    /**
     * Ajouter ou mettre à jour les statistiques mensuelles.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer',
            'etudiants' => 'required|integer',
            'professeurs' => 'required|integer',
            'classes' => 'required|integer',
            'revenus' => 'required|numeric',
            'depenses' => 'required|numeric',
            'salaires' => 'required|numeric',
            'charges' => 'required|numeric',
            'alertes' => 'required|integer',
            'reste' => 'required|numeric',
        ]);

        $stat = StatistiqueMensuelle::updateOrCreate(
            ['mois' => $request->mois, 'annee' => $request->annee],
            $request->only([
                'etudiants', 'professeurs', 'classes',
                'revenus', 'depenses', 'salaires',
                'charges', 'alertes', 'reste'
            ])
        );

        return response()->json([
            'message' => 'Statistique enregistrée avec succès.',
            'data' => $stat
        ]);
    }
}
