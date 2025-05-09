<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Utilisateur;
use App\Models\Paiement; // Ajouté
use App\Models\SalaireMensuel; // Ajouté
use App\Models\PaiementMensuel; // Ajouté pour panierDetail et getMoisPaiements
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfesseurController extends Controller
{
    public function destroyEtudiant($professeurId, $etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        if ($etudiant->professeur_id != $professeurId) {
            return response()->json(['message' => 'Cet étudiant n’appartient pas à ce professeur'], 403);
        }

        $montantEtudiant = $etudiant->montant_a_payer;
        $etudiant->delete();

        $professeur = Professeur::findOrFail($professeurId);
        $totalMontants = $professeur->etudiants()->sum('montant_a_payer');
        $salaire = ($professeur->pourcentage / 100) * $totalMontants + $professeur->prime;
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
        return $prof->matieres()
            ->wherePivot('classe_id', $classeId)
            ->get();
    }

    public function matieresAvecFiliere($professeurId, $classeId, $filiereId)
    {
        $prof = Professeur::findOrFail($professeurId);
        return $prof->matieres()
            ->wherePivot('classe_id', $classeId)
            ->get();
    }

    public function index()
    {
        $professeurs = Professeur::with('utilisateur')->get();
        return response()->json($professeurs);
    }

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
            'pourcentage' => 'required|numeric|min:0|max:100',
            'prime' => 'nullable|numeric|min:0',
        ]);

        $professeur = Professeur::create([
            'user_id' => $request->user_id,
            'nom' => $request->nom,
            'email' => $request->email,
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

    public function update(Request $request, $id)
    {
        $professeur = Professeur::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:utilisateurs,email,' . $professeur->user_id,
            'niveau_enseignement' => 'sometimes|required|string|max:255',
            'diplome' => 'sometimes|required|string|max:255',
            'date_embauche' => 'sometimes|required|date',
            
        ]);

        $professeur->update($request->all());

        return response()->json($professeur, 200);
    }
    public function calculerSalaire(Request $request, $id)
    {
        $professeur = Professeur::findOrFail($id);
    
        // Validation des champs reçus
        $request->validate([
            'prime' => 'required|numeric',
            'pourcentage' => 'required|numeric',
        ]);
    
        // Mise à jour des valeurs prime et pourcentage dans la base de données
        $professeur->prime = $request->prime;
        $professeur->pourcentage = $request->pourcentage;
        $professeur->save();  // Sauvegarde dans la BD
    
        // Calcul du salaire après mise à jour des données
        $totalMontants = $professeur->etudiants->sum(function ($etudiant) {
            return $etudiant->montant_a_payer;
        });
    
        $salaire = ($professeur->pourcentage / 100) * $totalMontants + $professeur->prime;
        $professeur->total = $salaire;
        $professeur->save(); 
    
        return response()->json(['salaire' => $salaire], 200);
    }
    
    public function calculerSalaireMensuel(Request $request, $id)
    {
        try {
            $professeur = Professeur::findOrFail($id);
            $request->validate([
                'prime' => 'required|numeric',
                'pourcentage' => 'required|numeric',
            ]);
            $prime = $request->input('prime', 0);
            $pourcentage = $request->input('pourcentage', 0);
            $mois = (int) $request->input('mois');
            $annee = $request->input('annee', now()->year);
         
    
            if ($mois < 1 || $mois > 12 || $annee < 2000 ) {
                return response()->json(['message' => 'Mois ou année invalide'], 400);
            }
    
            $etudiants = Etudiant::whereHas('professeurs', function($query) use ($id) {
                $query->where('professeur_id', $id);
            })
            ->whereHas('paiementsMensuels', function($q) use ($mois) {
                $q->where('mois', $mois)
                  ->where('est_paye', 1);
            })
            ->get();
    
            $totalMontants = $etudiants->sum('montant_a_payer');
            
    
            if ($totalMontants <= 0) {
                return response()->json(['message' => 'Montant total à payer est nul ou inférieur à zéro'], 400);
            }
    
            $salaire = ($totalMontants * ($pourcentage / 100)) + $prime;
    
            // Vérifier si le salaire pour ce mois et année existe déjà
            $salaireMensuelExist = SalaireMensuel::where('professeur_id', $id)
                ->where('mois', $mois)
                ->where('annee', $annee)
                ->first();
    
            if ($salaireMensuelExist) {
                // Si le salaire existe déjà, on le met à jour
                $salaireMensuelExist->update([
                    'total_paiements' => $totalMontants,
                    'salaire' => $salaire,
                ]);
                return response()->json([
                    'message' => 'Salaire mis à jour avec succès',
                    'salaire' => $salaire,
                    'nombre_etudiants' => $etudiants->count(),
                    'total_montants' => $totalMontants
                ]);
            }
          
            // Si le salaire n'existe pas, on le crée
            SalaireMensuel::create([
                'professeur_id' => $id,
                'mois' => $mois,
                'annee' => $annee,
                'total_paiements' => $totalMontants,
                'salaire' => $salaire,
            ]);
    
            return response()->json([
                'salaire' => $salaire,
                'nombre_etudiants' => $etudiants->count(),
                'total_montants' => $totalMontants
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'erreur' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    
    public function historiqueSalaire($professeurId)
    {
        $salaires = SalaireMensuel::where('professeur_id', $professeurId)
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();

        return response()->json($salaires);
    }

    public function destroy($id)
    {
        $professeur = Professeur::findOrFail($id);
        $professeur->delete();

        return response()->json(['message' => 'Professeur supprimé avec succès'], 200);
    }

    public function panierDetail($professeurId, Request $request)
    {
        $mois = $request->input('mois');

        $professeur = Professeur::with(['etudiants' => function ($query) use ($mois) {
            $query->whereHas('paiementsMensuels', function ($q) use ($mois) {
                $q->where('est_paye', 1);
                if ($mois) {
                    $q->where('mois', $mois);
                }
            })->with(['paiementsMensuels' => function ($q) use ($mois) {
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
        $mois = PaiementMensuel::whereHas('etudiant', function ($q) use ($professeurId) {
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
        $professeur = Professeur::findOrFail($professeurId);
        $paiements = $professeur->paiementsMensuels($mois);
        return response()->json($paiements);
    }

    public function affichierinfo($id)
    {
        $prof = Professeur::find($id);

        if (!$prof) {
            return response()->json(['error' => 'Professeur non trouvé'], 404);
        }

        return response()->json($prof);
    }

    public function getMatieres(Request $request)
    {
        $professeur_id = $request->query('professeur_id');
        $classe_id = $request->query('classe_id');

        $matieres = DB::table('prof_matiere_classe')
            ->join('matieres', 'prof_matiere_classe.matiere_id', '=', 'matieres.id')
            ->where('professeur_id', $professeur_id)
            ->where('classe_id', $classe_id)
            ->select('matieres.id', 'matieres.nom')
            ->get();

        return response()->json($matieres);
    }
}
