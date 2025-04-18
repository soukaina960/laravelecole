<?php

namespace App\Http\Controllers;

use App\Models\PaiementMensuel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EtudiantProfesseur;
use Illuminate\Support\Facades\DB;

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
        // Validation des données
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'mois' => 'required|date_format:Y-m', // Le mois doit être au format "YYYY-MM"
        ]);

        // Enregistrement du paiement mensuel
        $paiement = PaiementMensuel::create([
            'etudiant_id' => $request->etudiant_id,
            'mois' => $request->mois, // Le mois est récupéré directement du formulaire
            'date_paiement' => Carbon::now()->toDateString(), // Date de paiement actuelle
            'est_paye' => true, // Marque comme payé
        ]);

        return response()->json($paiement, 201);
    }

    // Méthode pour afficher un paiement mensuel spécifique
    public function show($id)
    {
        $paiement = PaiementMensuel::find($id);
        
        if (!$paiement) {
            return response()->json(['message' => 'Paiement non trouvé'], 404);
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
            return response()->json(['message' => 'Paiement non trouvé'], 404);
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
            return response()->json(['message' => 'Paiement non trouvé'], 404);
        }

        $paiement->delete();
        return response()->json(['message' => 'Paiement supprimé avec succès']);
    }

    // Méthode pour récupérer les paiements d'un étudiant par mois
    public function listePaiements($etudiantId)
    {
        $paiements = PaiementMensuel::where('etudiant_id', $etudiantId)->get();
    
        if ($paiements->isEmpty()) {
            return response()->json(['message' => 'Aucun paiement trouvé'], 404);
        }
    
        return response()->json($paiements);
    }

    // Méthode pour marquer un paiement comme payé
    public function payer($paiementId)
    {
        $paiement = PaiementMensuel::findOrFail($paiementId);

        if ($paiement->est_paye) {
            return response()->json(['error' => 'Le paiement a déjà été effectué'], 400);
        }

        $paiement->update([
            'est_paye' => true,
            'date_paiement' => Carbon::now()->toDateString(),
        ]);

        return response()->json($paiement);
    }
    public function getPaiements($professeurId, $mois)
    {
        try {
            if (!is_numeric($professeurId)) {
                return response()->json(['error' => 'ID professeur invalide'], 400);
            }
    
            if (!preg_match('/^(\d{4})-(\d{2})$/', $mois, $matches)) {
                return response()->json(['error' => 'Format du mois invalide'], 400);
            }
    
            $dbMonthFormat = $matches[1] . '-' . $matches[2]; // "YYYY-MM"
    
            $paiements = DB::table('etudiant_professeur')
                ->join('etudiants', 'etudiants.id', '=', 'etudiant_professeur.etudiant_id')
                ->leftJoin('paiements_mensuels', function($join) use ($dbMonthFormat) {
                    $join->on('etudiant_professeur.etudiant_id', '=', 'paiements_mensuels.etudiant_id')
                         ->where('paiements_mensuels.mois', 'LIKE', $dbMonthFormat . '%');
                })
                ->where('etudiant_professeur.professeur_id', $professeurId)
                ->select(
                    'etudiants.id as etudiant_id',
                    DB::raw('CONCAT(etudiants.prenom, " ", etudiants.nom) as nom_complet'),
                    'paiements_mensuels.mois',
                    'paiements_mensuels.est_paye',
                    'paiements_mensuels.date_paiement'
                )
                ->orderBy('etudiants.nom')
                ->orderBy('etudiants.prenom')
                ->get();
    
            return response()->json($paiements);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des paiements',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
}
