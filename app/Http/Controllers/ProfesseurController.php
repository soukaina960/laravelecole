<?php

namespace App\Http\Controllers;
use App\Models\Etudiant; // Ajoutez cette ligne
use App\Models\Professeur;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Illuminate\Support\Facades\DB;



class ProfesseurController extends Controller
{
    public function destroyEtudiant($professeurId, $etudiantId)
    {
        // Trouver l'étudiant
        $etudiant = Etudiant::findOrFail($etudiantId);
        
        // Vérifier que l'étudiant appartient bien au professeur
        if ($etudiant->professeur_id != $professeurId) {
            return response()->json(['message' => 'Cet étudiant ne appartient pas à ce professeur'], 403);
        }
        
        // Récupérer le montant de l'étudiant avant suppression
        $montantEtudiant = $etudiant->montant_a_payer;
        
        // Supprimer l'étudiant
        $etudiant->delete();
        
        // Mettre à jour le salaire du professeur
        $professeur = Professeur::findOrFail($professeurId);
        
        // Calculer le nouveau total des montants des étudiants restants
        $totalMontants = $professeur->etudiants()->sum('montant_a_payer');
        
        // Recalculer le salaire
        $salaire = ($professeur->pourcentage / 100) * $totalMontants + $professeur->prime;
        
        // Mettre à jour le salaire du professeur
        $professeur->total = $salaire;
        $professeur->save();
        
        return response()->json([
            'message' => 'Étudiant supprimé avec succès et salaire mis à jour',
            'nouveau_salaire' => $salaire,
            'montant_soustrait' => $montantEtudiant * ($professeur->pourcentage / 100)
        ], 200);
    }
    public function matieresSansFiliere($professeurId, $classeId)
    {
        $prof = Professeur::findOrFail($professeurId);
        // relation many-to-many sans filière
        return $prof->matieres()
            ->wherePivot('classe_id', $classeId)
            ->get();
    }

    public function matieresAvecFiliere($professeurId, $classeId, $filiereId)
    {
        $prof = Professeur::findOrFail($professeurId);
        // relation many-to-many avec classe + filière
        return $prof->matieres()
            ->wherePivot('classe_id', $classeId)
           
            ->get();
    }
    // Récupérer tous les professeurs avec l'utilisateur lié
    public function index()
    {
        $professeurs = Professeur::with('utilisateur')->get();
        return response()->json($professeurs);
    }

    // Créer un nouveau professeur
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:utilisateurs,id',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:utilisateurs,email',
            'specialite' => 'required|string|max:255',
            'niveau_enseignement' => 'required|string|max:255',
            'diplome' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);
     

        $professeur = Professeur::create([
            'user_id' => $request->user_id,
            'nom'=> $request->nom,
            'email'=> $request->email,
            'specialite' => $request->specialite,
            'niveau_enseignement' => $request->niveau_enseignement,
            'diplome' => $request->diplome,
            'date_embauche' => $request->date_embauche,
        ]);

        return response()->json($professeur, 201);
    }
    public function show($id)
    {
        $professeur = Professeur::with('utilisateur')->findOrFail($id);
        return response()->json($professeur);
    }
    

    // Modifier un professeur
    public function update(Request $request, $id)
    {
        $professeur = Professeur::findOrFail($id);

        $request->validate([
            'niveau_enseignement' => 'sometimes|required|string|max:255',
            'diplome' => 'sometimes|required|string|max:255',
            'date_embauche' => 'sometimes|required|date',
        ]);

        $professeur->update($request->all());

        return response()->json($professeur, 200);
    }
    
    public function calculerSalaireMensuel(Request $request, $id)
{
    $request->validate([
        'prime' => 'required|numeric',
        'pourcentage' => 'required|numeric',
        'mois' => 'required|integer|min:1|max:12'
    ]);

    $prime = $request->prime;
    $pourcentage = $request->pourcentage;
    $mois = $request->mois;

    // Étape 1 : Trouver les classes enseignées par le professeur
    $classeIds = DB::table('prof_matiere_classe')
        ->where('professeur_id', $id)
        ->pluck('classe_id');

    // Étape 2 : Trouver les étudiants dans ces classes
    $etudiants = DB::table('etudiants')
        ->whereIn('classe_id', $classeIds)
        ->get();

    // Étape 3 : Calculer les paiements payés dans le mois
    $totalMontants = 0;

    foreach ($etudiants as $etudiant) {
        $paiements = DB::table('paiement_mensuels') // ou le nom réel de ta table
            ->where('etudiant_id', $etudiant->id)
            ->where('mois', $mois)
            ->where('est_paye', true)
            ->get();

        foreach ($paiements as $paiement) {
            $totalMontants += $etudiant->montant_a_payer;
        }
    }

    $salaire = ($pourcentage / 100) * $totalMontants + $prime;

    return response()->json(['salaire' => $salaire], 200);
}

    public function historiqueSalaire($professeurId)
{
    $salaires = SalaireMensuel::where('professeur_id', $professeurId)
        ->orderBy('annee', 'desc')
        ->orderBy('mois', 'desc')
        ->get();

    return response()->json($salaires);
}

    
    // Supprimer un professeur
    public function destroy($id)
    {
        $professeur = Professeur::findOrFail($id);
        $professeur->delete();

        return response()->json(['message' => 'Professeur supprimé avec succès'], 200);
    }
    public function panierDetail($professeurId, Request $request)
    {
        $mois = $request->input('mois');
        
        $professeur = Professeur::with(['etudiants' => function($query) use ($mois) {
            $query->whereHas('paiementsMensuels', function($q) use ($mois) {
                $q->where('est_paye', 1);
                if ($mois) {
                    $q->where('mois', $mois);
                }
            })->with(['paiementsMensuels' => function($q) use ($mois) {
                if ($mois) {
                    $q->where('mois', $mois);
                }
            }]);
        }])->findOrFail($professeurId);

        $pourcentage = $professeur->pourcentage;

        $eleves = $professeur->etudiants->map(function ($etudiant) use ($pourcentage) {
            $paiement = $etudiant->paiementsMensuels->first();
            $montantPaye = $paiement ? $etudiant->montant_a_payer : 0;
            $partProf = $montantPaye > 0 ? round($montantPaye * ($pourcentage / 100), 2) : 0;

            return [
                'id' => $etudiant->id,
                'nom_complet' => $etudiant->nom . ' ' . $etudiant->prenom,
                'total_paye' => $montantPaye,
                'date_paiement' => $paiement ? $paiement->date_paiement : null,
                'part_professeur' => $partProf,
                'mois_paiement' => $paiement ? $paiement->mois : null
            ];
        })->filter(function ($eleve) {
            return $eleve['total_paye'] > 0;
        })->values();

        $totalProf = $eleves->sum('part_professeur');

        return response()->json([
            'success' => true,
            'data' => [
                'professeur' => [
                    'id' => $professeur->id,
                    'nom' => $professeur->nom,
                    'prenom' => $professeur->prenom,
                    'pourcentage' => $pourcentage
                ],
                'eleves' => $eleves,
                'total_professeur' => $totalProf,
                'mois_filtre' => $mois
            ]
        ]);
    }
    public function getMoisPaiements($professeurId)
{
    $mois = PaiementMensuel::whereHas('etudiant', function($q) use ($professeurId) {
        $q->where('professeur_id', $professeurId);
    })
    ->where('est_paye', 1)
    ->select('mois')
    ->distinct()
    ->orderBy('mois', 'desc')
    ->get()
    ->pluck('mois');

    return response()->json([
        'success' => true,
        'mois' => $mois
    ]);
}
    
    
public function getEtudiantsAvecPaiements($professeurId, $mois = null)
    {
        // Trouver le professeur par son ID
        $professeur = Professeur::findOrFail($professeurId);

        // Récupérer les étudiants et leurs paiements mensuels
        $paiements = $professeur->paiementsMensuels($mois);

        return response()->json($paiements);
    }
    public function affichierinfo($id)
    {
        // Trouver le professeur avec l'ID
        $prof = Professeur::find($id);

        // Vérifier si le professeur existe
        if (!$prof) {
            return response()->json(['error' => 'Professeur non trouvé'], 404);
        }

        // Retourner les informations du professeur
        return response()->json($prof);
    }
}
