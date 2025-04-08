<?php

namespace App\Http\Controllers;

use App\Models\PaiementMensuel;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Carbon\Carbon;


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
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
        ]);
    
        $paiement = PaiementMensuel::create([
            'etudiant_id' => $request->etudiant_id,
            'mois' => now()->format('Y-m'),
            'date_paiement' => Carbon::now()->toDateString(), 
            'est_paye' => true, 
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
            'est_paye' => 'required|boolean',
        ]);

        $paiement = PaiementMensuel::find($id);

        if (!$paiement) {
            return response()->json(['message' => 'Paiement not found'], 404);
        }

        $paiement->update([
            'etudiant_id' => $request->etudiant_id,
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
