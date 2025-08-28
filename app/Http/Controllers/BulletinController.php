<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\Evaluation;
use App\Models\Etudiant;
use App\Models\Semestre;
use App\Models\AnneeScolaire;
use App\Models\Professeur;
use App\Models\Matiere;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\ParentModel;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    /**
     * Create a new bulletin for a student
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBulletin(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'etudiant_id' => 'required|exists:etudiants,id',
            'semestre_id' => 'required|exists:semestres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Get evaluations for the student in the given academic year
        $evaluations = Evaluation::where('etudiant_id', $request->etudiant_id)
                                ->where('annee_scolaire_id', $request->annee_scolaire_id)
                                ->get();

        if ($evaluations->isEmpty()) {
            return response()->json([
                'message' => 'Aucune évaluation trouvée pour cet étudiant.',
            ], 404);
        }

        $totalPondere = 0;
        $totalFacteurs = 0;

        foreach ($evaluations as $evaluation) {
            if ($evaluation->note_finale !== null && $evaluation->facteur !== null) {
                $totalPondere += $evaluation->note_finale * $evaluation->facteur;
                $totalFacteurs += $evaluation->facteur;
            }
        }

        $moyenneGenerale = $totalFacteurs > 0 ? round($totalPondere / $totalFacteurs, 2) : 0;

        // Create bulletin
        $bulletin = Bulletin::create([
            'etudiant_id' => $request->etudiant_id,
            'semestre_id' => $request->semestre_id,
            'annee_scolaire_id' => $request->annee_scolaire_id,
            'moyenne_generale' => $moyenneGenerale,
            'est_traite' => true,
        ]);

        return response()->json([
            'message' => 'Bulletin créé avec succès.',
            'bulletin' => $bulletin,
        ], 201);
    }

    /**
     * Get the latest processed bulletin for a student
     *
     * @param int $etudiantId
     * @return \Illuminate\Http\JsonResponse
     */
public function getBulletinsByEtudiant($etudiantId, Request $request)
{
    $query = Bulletin::where('etudiant_id', $etudiantId)
                    ->with(['etudiant', 'semestre', 'anneeScolaire']);

    // Optional filters
    if ($request->has('annee_id')) {
        $query->where('annee_scolaire_id', $request->annee_id);
    }

    if ($request->has('semestre_id')) {
        $query->where('semestre_id', $request->semestre_id);
    }

    // Only show processed bulletins
    $query->where('est_traite', true);

    $bulletins = $query->get();

    if ($bulletins->isEmpty()) {
        return response()->json(['message' => 'Aucun bulletin trouvé.'], 404);
    }

    // Convert to array and ensure UTF-8 encoding
    $data = $bulletins->toArray();
    array_walk_recursive($data, function (&$value) {
        if (is_string($value)) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
    });

    return response()->json(['data' => $data], 200);
}

    /**
     * Get bulletin for a parent's child
     *
     * @param int $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBulletinForParent($parentId)
    {
        // Get the student associated with the parent
        $etudiant = Etudiant::where('parent_id', $parentId)->first();

        if (!$etudiant) {
            return response()->json(['message' => 'Étudiant non trouvé pour ce parent.'], 404);
        }

        // Get the latest processed bulletin for the student
        $bulletin = Bulletin::where('etudiant_id', $etudiant->id)
                            ->where('est_traite', true)
                            ->with(['semestre', 'anneeScolaire'])
                            ->latest()
                            ->first();

        if (!$bulletin) {
            return response()->json(['message' => 'Aucun bulletin traité trouvé.'], 404);
        }

        return response()->json([
            'etudiant' => $etudiant->only(['id', 'nom', 'prenom']),
            'moyenne_generale' => $bulletin->moyenne_generale,
            'semestre' => $bulletin->semestre,
            'annee_scolaire' => $bulletin->anneeScolaire,
        ], 200);
    }

    /**
     * Generate a PDF bulletin for a parent
     *
     * @param int $etudiant_id
     * @param int $semestre_id
     * @param int $annee_scolaire_id
     * @return \Illuminate\Http\Response
     */
 
 public function generatePdf($bulletinId)
{
    // Chargement des relations avec eager loading optimisé
    $bulletin = Bulletin::with([
        'etudiant.classe', // Si l'étudiant appartient à une classe
        'semestre',
        'anneeScolaire',
        'evaluations' => function($query) {
            $query->with('matiere')
                 ->orderBy('created_at', 'desc');
        }
    ])->findOrFail($bulletinId);

    // Vérification des autorisations plus robuste
    $this->authorize('view', $bulletin);

    // Calcul de la moyenne générale si nécessaire
    $moyenneGenerale = $bulletin->evaluations->avg('note') ?? 0;

    // Préparation des données pour la vue
    $data = [
        'bulletin' => $bulletin,
        'moyenneGenerale' => number_format($moyenneGenerale, 2),
        'date' => now()->translatedFormat('d F Y'), // Format plus lisible
        'establishment' => config('app.establishment_name', 'Nom par défaut'),
        'logoPath' => public_path('images/logo.png') // Chemin vers le logo
    ];

    // Configuration du PDF
    $pdf = PDF::loadView('bulletins.bulletin_pdf', $data)
              ->setPaper('a4', 'portrait')
              ->setOptions([
                  'isHtml5ParserEnabled' => true,
                  'isRemoteEnabled' => true,
                  'defaultFont' => 'sans-serif'
              ]);

    // Nom du fichier plus descriptif
    $filename = sprintf(
        'bulletin_%s_%s_%s.pdf',
        Str::slug($bulletin->etudiant->nom),
        Str::slug($bulletin->semestre->nom),
        now()->format('Y-m-d')
    );

    return $pdf->stream($filename);
}

    /**
     * Get all bulletins for a student
     *
     * @param int $etudiantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBulletinsForStudent($etudiantId)
    {
        $bulletins = Bulletin::where('etudiant_id', $etudiantId)
                            ->with(['semestre', 'anneeScolaire'])
                            ->orderBy('annee_scolaire_id')
                            ->orderBy('semestre_id')
                            ->get();

        return response()->json($bulletins, 200);
    }

    /**
     * Get bulletin details with evaluations
     *
     * @param int $bulletinId
     * @return \Illuminate\Http\JsonResponse
     */
 /**
 * Récupère les détails complets d'un bulletin spécifique
 *
 * @param int $bulletinId
 * @return \Illuminate\Http\JsonResponse
 */
public function getBulletinDetails($bulletinId)
{
    try {
        // Récupérer le bulletin avec ses relations
        $bulletin = Bulletin::with([
            'etudiant.classroom',
            'semestre',
            'anneeScolaire',
        ])->findOrFail($bulletinId);

        // Récupérer les évaluations liées à cet étudiant pour le même semestre et année scolaire
        $evaluations = Evaluation::with(['matiere', 'professeur'])
            ->where('etudiant_id', $bulletin->etudiant_id)
            ->where('semestre_id', $bulletin->semestre_id)
            ->where('annee_scolaire_id', $bulletin->annee_scolaire_id)
            ->get();

        // Calcul de la moyenne si elle est vide
        if ($bulletin->moyenne_generale === null) {
            $totalPondere = 0;
            $totalFacteurs = 0;

            foreach ($evaluations as $evaluation) {
                if (!is_null($evaluation->note_finale) && !is_null($evaluation->facteur)) {
                    $totalPondere += $evaluation->note_finale * $evaluation->facteur;
                    $totalFacteurs += $evaluation->facteur;
                }
            }

            $moyenne = $totalFacteurs > 0 ? round($totalPondere / $totalFacteurs, 2) : 0;
            $bulletin->moyenne_generale = $moyenne;
            $bulletin->save(); // mise à jour de la base
        }

        // Formatage de la réponse
        $response = [
            'id' => $bulletin->id,
            'etudiant' => [
                'id' => $bulletin->etudiant->id,
                'nom' => $bulletin->etudiant->nom,
                'prenom' => $bulletin->etudiant->prenom,
                'classe' => $bulletin->etudiant->classroom->nom ?? 'Non défini',
            ],
            'semestre' => $bulletin->semestre->nom,
            'annee_scolaire' => $bulletin->anneeScolaire->annee,
            'moyenne_generale' => $bulletin->moyenne_generale,
            'est_traite' => $bulletin->est_traite,
            'created_at' => $bulletin->created_at,
            'evaluations' => $evaluations->map(function($evaluation) {
                return [
                    'matiere' => $evaluation->matiere->nom,
                    'professeur' => $evaluation->professeur->nom . ' ' . $evaluation->professeur->prenom,
                    'note_finale' => $evaluation->note_finale,
                    'facteur' => $evaluation->facteur,
                    'remarque' => $evaluation->remarque,
                ];
            }),
        ];

        return response()->json($response, 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'Bulletin non trouvé.'], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération du bulletin.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function generateBulletinPourParent($etudiant_id, $semestre_id, $annee_scolaire_id)
{
    $etudiant = Etudiant::findOrFail($etudiant_id);
    
    $semestre = Semestre::findOrFail($semestre_id);
    $anneeScolaire = AnneeScolaire::findOrFail($annee_scolaire_id);

    // Get class_id of the student
    $classId = $etudiant->classe_id;

    // Count how many students belong to the same class
    $nombreEtudiantsDansClasse = Etudiant::where('classe_id', $classId)->count();

    $evaluations = Evaluation::where('etudiant_id', $etudiant_id)
        ->where('semestre_id', $semestre_id)
        ->where('annee_scolaire_id', $annee_scolaire_id)
        ->with(['professeur', 'matiere'])
        ->get();

    if ($evaluations->isEmpty()) {
        return response()->json([
            'message' => 'Le bulletin n\'est pas encore prêt pour cet élève.'
        ], 404);
    }

    // Calcul moyenne générale
    $totalNotes = 0;
    $noteCount = 0;

    foreach ($evaluations as $eval) {
        if ($eval->note1 !== null) {
            $totalNotes += $eval->note1;
            $noteCount++;
        }
        if ($eval->note2 !== null) {
            $totalNotes += $eval->note2;
            $noteCount++;
        }
        if ($eval->note3 !== null) {
            $totalNotes += $eval->note3;
            $noteCount++;
        }
        if ($eval->note4 !== null) {
            $totalNotes += $eval->note4;
            $noteCount++;
        }
        if ($eval->note_finale !== null) {
            $totalNotes += $eval->note_finale;
            $noteCount++;
        }
    }

    $moyenneGenerale = $noteCount > 0 ? round($totalNotes / $noteCount, 2) : 0;

    $data = [
        'etudiant' => $etudiant,
        'evaluations' => $evaluations,
        'semestre' => $semestre,
        'annee_scolaire' => $anneeScolaire,
        'moyenneGenerale' => $moyenneGenerale,
        'nombreEtudiantsDansClasse' => $nombreEtudiantsDansClasse, // this is the count
    ];

    $pdf = PDF::loadView('bulletins.bulletin_pdf', $data);

    return $pdf->download('bulletin_' . $etudiant->nom . '.pdf');
}

public function existe($enfantId, $semestreId, $anneeId)
{
    // Exemple simple : vérifier si un bulletin existe en base
    $exists = Bulletin::where('etudiant_id', $enfantId)
        ->where('semestre_id', $semestreId)
        ->where('annee_scolaire_id', $anneeId)
        ->exists();

    return response()->json(['existe' => $exists]);
}

}