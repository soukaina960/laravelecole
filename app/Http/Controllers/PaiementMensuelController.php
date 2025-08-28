<?php

namespace App\Http\Controllers;

use App\Models\PaiementMensuel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EtudiantProfesseur;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ParentModel;
use App\Models\Utilisateur;
use App\Models\Professeur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaiementMensuelController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id'
        ]);
    
        // Get current month in the correct format
        $moisCourant = Carbon::now()->format('m'); // Or 'Y-m-d' depending on your needs
        
    
        // Check for existing payment
        $paiementExistant = PaiementMensuel::where('etudiant_id', $request->etudiant_id)
                                            ->where('mois', $moisCourant)
                                            ->first();
    
        if ($paiementExistant) {
            return response()->json([
                'message' => 'Un paiement existe déjà pour ce mois',
                'paiement' => $paiementExistant
            ], 409);
        }
    
        // Create payment
        $paiement = PaiementMensuel::create([
            'etudiant_id' => $request->etudiant_id,
            'mois' => $moisCourant, // Format depends on your column definition
            'date_paiement' => Carbon::now()->toDateString(),
            'est_paye' => true,
        ]);
    
        return response()->json($paiement, 201);
    }
    
    // PaiementController.php

    public function getCountEtudiantsSansPaiement()
    {
        $countEtudiantsSansPaiement = DB::table('etudiants')
            ->leftJoin('paiements_mensuels', 'etudiants.id', '=', 'paiements_mensuels.etudiant_id')
            ->whereNull('paiements_mensuels.etudiant_id') // Condition pour récupérer ceux qui ne sont pas dans la table paiements_mensuels
            ->count(); // Retourne le nombre d'étudiants sans paiement
    
        return response()->json([
            'count' => $countEtudiantsSansPaiement
        ]);
    }
    
    

    // Méthode pour afficher tous les paiements mensuels
    public function index()
    {
        $paiements = PaiementMensuel::all();
        return response()->json($paiements);
    }


    // Méthode pour créer un paiement mensuel
   
    

    
    
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
    // Validation simple manuelle
    if (!is_numeric($professeurId) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mois)) {
        return response()->json(['error' => 'Paramètres invalides'], 400);
    }

   list($annee, $moisNum) = explode('-', $mois);
$moisInt = (int)$moisNum; // convertit '01' en 1, '02' en 2 etc.

$paiements = DB::table('etudiant_professeur')
    ->join('etudiants', 'etudiants.id', '=', 'etudiant_professeur.etudiant_id')
  ->join('paiements_mensuels', function($join) use ($moisNum, $annee) {
    $join->on('etudiant_professeur.etudiant_id', '=', 'paiements_mensuels.etudiant_id')
         ->where('paiements_mensuels.mois', '=', $moisNum)
         ->whereYear('paiements_mensuels.date_paiement', '=', $annee);
})

    ->where('etudiant_professeur.professeur_id', $professeurId)
    ->select(
        'etudiants.id as etudiant_id',
        DB::raw('CONCAT(etudiants.prenom, " ", etudiants.nom) as nom_complet'),
        'paiements_mensuels.mois',
        'paiements_mensuels.est_paye',
        'paiements_mensuels.date_paiement',
        DB::raw("
            CASE
                WHEN paiements_mensuels.est_paye = 1 THEN 'Payé'
                WHEN paiements_mensuels.est_paye = 0 THEN 'Non payé'
                ELSE 'Non renseigné'
            END as statut_paiement
        ")
    )
    ->orderBy('etudiants.nom')
    ->orderBy('etudiants.prenom')
    ->get();

$totalPaiements = DB::table('salaire_professeurs')
    ->where('professeur_id', $professeurId)
    ->where('annee', $annee)
    ->where('mois', $moisInt)
    ->select('total_paiements')
    ->first();


    $total = $totalPaiements ? $totalPaiements->total_paiements : 0;

    return response()->json([
        'paiements' => $paiements,
        'total_paiements' => $total,
    ]);
}



    public function resetPaiementsMoisPrecedent()
{
    // Obtenez le mois précédent
    $moisPrecedent = Carbon::now()->subMonth()->format('Y-m');

    try {
        // Réinitialiser les paiements du mois précédent à 'non payé' (est_paye = false)
        $paiements = PaiementMensuel::where('mois', $moisPrecedent)->update(['est_paye' => false]);

        return response()->json([
            'message' => 'Les paiements du mois précédent ont été réinitialisés.',
            'paiements_reset' => $paiements
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors de la réinitialisation des paiements.',
            'details' => $e->getMessage()
        ], 500);
    }




}


   public function getPaiementsByMois($parent_id, $mois)
{
    $mois_numero = $this->convertirMoisEnNumero($mois);

    if (!$mois_numero) {
        return response()->json(['message' => 'Mois invalide.'], 400);
    }

    // Remplacez whereMonth par where car le champ contient déjà le numéro du mois
    $paiements = PaiementMensuel::where('mois', $mois_numero)
        ->whereHas('etudiant', function ($query) use ($parent_id) {
            $query->where('parent_id', $parent_id);
        })
        ->with('etudiant') // Chargement anticipé des données étudiant
        ->get();

    return response()->json([
        'success' => true,
        'data' => $paiements,
        'message' => $paiements->isEmpty() ? 'Aucun paiement trouvé pour ce mois.' : null
    ]);
}

public function generateReceipt($parent_id, $paiement_id)
{
    $paiement = PaiementMensuel::where('id', $paiement_id)
        ->whereHas('etudiant', function ($query) use ($parent_id) {
            $query->where('parent_id', $parent_id);
        })
        ->first();

    if (!$paiement) {
        return response()->json(['message' => 'Paiement non trouvé ou non autorisé.'], 404);
    }

    $data = [
        'paiement' => $paiement,
        'etudiant' => $paiement->etudiant,
        'parent' => $paiement->etudiant->parent,
        'ecole' => 'Skolyx',
    ];

    $pdf = PDF::loadView('pdf.receipt', $data);
    return $pdf->download('recu_paiement_' . $paiement->id . '.pdf');
}
    // Ajoutez cette méthode
    private function convertirMoisEnNumero($mois)
    {
        $mois_array = [
            'janvier' => '01',
            'février' => '02',
            'mars' => '03',
            'avril' => '04',
            'mai' => '05',
            'juin' => '06',
            'juillet' => '07',
            'août' => '08',
            'septembre' => '09',
            'octobre' => '10',
            'novembre' => '11',
            'décembre' => '12',
        ];

        $mois = strtolower($mois);

        return $mois_array[$mois] ?? null;
    }

    
}
