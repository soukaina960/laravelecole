<?php

namespace App\Http\Controllers;

use App\Models\PaiementMensuel;
use App\Models\Etudiant;
use Illuminate\Http\Request;

class PaiementMensuelController extends Controller
{
    // Méthode pour afficher tous les paiements mensuels
    public function index()
    {
        $paiements = PaiementMensuel::all();
        return response()->json($paiements);
    }

    // Méthode pour créer un paiement mensuel
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'mois' => 'required|date',
            'est_paye' => 'required|boolean',
        ]);

        $paiement = PaiementMensuel::create([
            'etudiant_id' => $request->etudiant_id,
            'mois' => $request->mois,
            'est_paye' => $request->est_paye,
        ]);

        return response()->json($paiement, 201);
    }

    // Méthode pour afficher un paiement mensuel spécifique
    public function show($id)
    {
        $paiement = PaiementMensuel::find($id);
        
        if (!$paiement) {
            return response()->json(['message' => 'Paiement not found'], 404);
        }

        return response()->json($paiement);
    }

    // Méthode pour mettre à jour un paiement mensuel
    public function update(Request $request, $id)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'mois' => 'required|date',
            'est_paye' => 'required|boolean',
        ]);

        $paiement = PaiementMensuel::find($id);

        if (!$paiement) {
            return response()->json(['message' => 'Paiement not found'], 404);
        }

        $paiement->update([
            'etudiant_id' => $request->etudiant_id,
            'mois' => $request->mois,
            'est_paye' => $request->est_paye,
        ]);

        return response()->json($paiement);
    }

    // Méthode pour supprimer un paiement mensuel
    public function destroy($id)
    {
        $paiement = PaiementMensuel::find($id);

        if (!$paiement) {
            return response()->json(['message' => 'Paiement not found'], 404);
        }

        $paiement->delete();
        return response()->json(['message' => 'Paiement deleted successfully']);
    }
}
