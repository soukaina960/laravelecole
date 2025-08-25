<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\ParentModel ;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Professeur;
use App\Models\Matiere;
use App\Models\Semestre;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Note;

class EvaluationController extends Controller
{
    
    // Récupération des étudiants + leurs évaluations selon le professeur et la matière
    public function indexParClasseEtProfesseur($classeId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'professeur_id' => 'required|exists:professeurs,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'semestre_id' => 'required|exists:semestres,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $etudiants = Etudiant::where('classe_id', $classeId)
            ->with(['evaluations' => function($query) use ($request) {
                $query->where('professeur_id', $request->professeur_id)
                      ->where('matiere_id', $request->matiere_id)
                      ->where('annee_scolaire_id', $request->annee_scolaire_id)
                      ->where('semestre_id', $request->semestre_id);
            }])
            ->get();

        return response()->json($etudiants->map(function ($etudiant) {
            $evaluation = $etudiant->evaluations->first();
            return [
                'id' => $etudiant->id,
                'nom' => $etudiant->nom,
                'note1' => $evaluation->note1 ?? null,
                'note2' => $evaluation->note2 ?? null,
                'note3' => $evaluation->note3 ?? null,
                'note4' => $evaluation->note4 ?? null,
                'facteur' => $evaluation->facteur ?? 1,
                'note_finale' => $evaluation->note_finale ?? null,
                'remarque' => $evaluation->remarque ?? null,
                'semestre_id' => $evaluation->semestre_id ?? null,
                'matiere_id' => $evaluation->matiere_id ?? null,
                'annee_scolaire_id' => $evaluation->annee_scolaire_id ?? null,
            ];
        }));
    }
private function nettoyerUtf8($donnees)
{
    if (is_string($donnees)) {
        return mb_convert_encoding($donnees, 'UTF-8', 'UTF-8');
    } elseif (is_array($donnees)) {
        return array_map([$this, 'nettoyerUtf8'], $donnees);
    } elseif (is_object($donnees)) {
        foreach ($donnees as $cle => $valeur) {
            $donnees->$cle = $this->nettoyerUtf8($valeur);
        }
    }
    return $donnees;
}

    // Afficher les notes par étudiant ID
  public function show($etudiant_id)
{
    $etudiant = Etudiant::find($etudiant_id);

    if (!$etudiant) {
        return response()->json(['message' => 'Étudiant non trouvé'], 404);
    }

    $evaluations = Evaluation::where('etudiant_id', $etudiant_id)
        ->with(['matiere', 'professeur'])
        ->get();

    // Convertir en tableau et nettoyer les caractères UTF-8
    $data = [
        'etudiant' => $etudiant->toArray(),
        'evaluations' => $evaluations->toArray()
    ];

    $dataUtf8 = $this->nettoyerUtf8($data);

    return response()->json($dataUtf8);
}

    // Enregistrement ou mise à jour des notes
    public function store(Request $request)
    {
        try {
            Log::info($request->all());
    
            $validated = $request->validate([
                'professeur_id' => 'required|exists:professeurs,id',
                'notes' => 'required|array',
                'notes.*.etudiant_id' => 'required|exists:etudiants,id',
                'notes.*.annee_scolaire_id' => 'nullable|exists:annees_scolaires,id',
                'notes.*.semestre_id' => 'nullable|exists:semestres,id',
                'notes.*.matiere_id' => 'nullable|exists:matieres,id',
                'notes.*.note1' => 'nullable|numeric',
                'notes.*.note2' => 'nullable|numeric',
                'notes.*.note3' => 'nullable|numeric',
                'notes.*.note4' => 'nullable|numeric',
                'notes.*.facteur' => 'nullable|numeric',
                'notes.*.note_finale' => 'required|numeric',
                'notes.*.remarque' => 'nullable|string',
            ]);
    
            foreach ($request->notes as $note) {
                Evaluation::updateOrCreate(
                    [
    'etudiant_id' => $note['etudiant_id'],
    'professeur_id' => $request->professeur_id,
    'annee_scolaire_id' => $note['annee_scolaire_id'],
    'semestre_id' => $note['semestre_id'] ?? null,
    'matiere_id' => $note['matiere_id'] ?? null,
],

                    [
                        'note1' => $note['note1'] ?? null,
                        'note2' => $note['note2'] ?? null,
                        'note3' => $note['note3'] ?? null,
                        'note4' => $note['note4'] ?? null,
                        'facteur' => $note['facteur'] ?? 1,
                        'note_finale' => $note['note_finale'],
                        'remarque' => $note['remarque'] ?? '',
                    ]
                );
            }
    
            return response()->json(['message' => 'Notes enregistrées avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement des notes: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }
    
  // Exemple d'une méthode dans votre contrôleur pour récupérer les notes avec la matière
  public function getNotesEtudiant($etudiant_id, Request $request)
  {
      // Récupérer les paramètres d'année scolaire et de semestre depuis la requête
      $annee_scolaire_id = $request->query('annee_scolaire_id');
      $semestre_id = $request->query('semestre_id');
      
      // Récupérer les notes de l'étudiant, et associer avec la spécialité du professeur
     $notes = DB::table('evaluations')
    ->join('professeurs', 'evaluations.professeur_id', '=', 'professeurs.id')
    ->join('matieres', 'evaluations.matiere_id', '=', 'matieres.id') // 🔧 ajout du join avec matieres
    ->where('evaluations.etudiant_id', $etudiant_id)
    ->where('evaluations.annee_scolaire_id', $annee_scolaire_id)
    ->where('evaluations.semestre_id', $semestre_id)
    ->select(
        'evaluations.*',
        'matieres.nom as matiere_nom',
        'professeurs.nom as professeur_nom'
    )
    ->get();

return response()->json($notes);
    }
  public function getNotesByParentAndSemestre(Request $request)
  {
      $parentId = $request->query('parent_id');
      $semestreId = $request->query('semestre_id');
  
      if (!$parentId || !$semestreId) {
          return response()->json(['message' => 'parent_id et semestre_id requis'], 400);
      }
  
      // Récupérer l'étudiant lié à ce parent
      $parent = ParentModel::with('etudiant')->find($parentId);
  
      if (!$parent || !$parent->etudiant) {
          return response()->json(['message' => 'Parent ou étudiant non trouvé'], 404);
      }
  
      $etudiantId = $parent->etudiant->id;
  
      // ✅ Corrigé ici : Utilise Evaluation au lieu de Note
      $notes = Evaluation::where('etudiant_id', $etudiantId)
                   ->where('semestre_id', $semestreId)
                   ->with('professeur') // si tu veux récupérer la matière depuis la spécialité du professeur
                   ->get()
                   ->map(function ($note) {
                       return [
                           'matiere' => $note->professeur->specialite ?? 'Inconnue',
                           'note_finale' => $note->note_finale,
                           'remarque' => $note->remarque,
                       ];
                   });
  
      return response()->json($notes);
  }
  public function getEtudiantByParent($parentId)
{
    $parent = ParentModel::with('etudiant')->find($parentId);
    if (!$parent || !$parent->etudiant) {
        return response()->json(['message' => 'Parent ou étudiant non trouvé'], 404);
    }
    
    return response()->json($parent->etudiant);
}
public function getNotesByParent(Request $request)
{
    $parentId = $request->input('parent_id');
    $semestreId = $request->input('semestre_id');
    $anneeId = $request->input('annee_scolaire_id');

    if (!$parentId || !$semestreId || !$anneeId) {
        return response()->json(['message' => 'Paramètres manquants'], 400);
    }

    // Trouver les enfants de ce parent
    $enfants = Etudiant::where('parent_id', $parentId)->get();

    $notes = [];

    foreach ($enfants as $enfant) {
        $evaluations = Evaluation::where('etudiant_id', $enfant->id)
            ->where('semestre_id', $semestreId)
            ->where('annee_scolaire_id', $anneeId)
            ->with('matiere') // si tu as une relation vers la matière
            ->get()
            ->map(function ($eval) {
                return [
                    'matiere' => $eval->matiere->nom ?? 'Matière inconnue', // si relation existe
                    'note1' => $eval->note1,
                    'note2' => $eval->note2,
                    'note3' => $eval->note3,
                    'note4' => $eval->note4,
                    'note_finale' => $eval->note_finale,
                    'remarque' => $eval->remarque,
                ];
            });

        $notes[$enfant->id] = $evaluations;
    }

    return response()->json($notes);
}



public function generateBulletin($etudiant_id, $semestre_id, $annee_scolaire_id)
{
    $etudiant = Etudiant::findOrFail($etudiant_id);

    $evaluations = Evaluation::where('etudiant_id', $etudiant_id)
        ->where('semestre_id', $semestre_id)
        ->where('annee_scolaire_id', $annee_scolaire_id)
        ->with(['professeur', 'matiere']) // تأكد ان العلاقة موجودة
        ->get();

    $data = [
        'etudiant' => $etudiant,
        'evaluations' => $evaluations,
        'semestre' => $semestre_id,
        'annee_scolaire' => $annee_scolaire_id,
    ];

    $pdf = PDF::loadView('bulletins.bulletin_pdf', $data);

    return $pdf->download('bulletin_' . $etudiant->nom . '.pdf');
}public function afficherBulletin($etudiant_id, $semestre_id, $annee_scolaire_id)
{
    // Vérifier si l'étudiant existe
    $etudiant = Etudiant::find($etudiant_id);
    
    if (!$etudiant) {
        return response()->json([
            'success' => false,
            'message' => 'Étudiant non trouvé'
        ], 404);
    }

    // Vérifier si le semestre et l'année scolaire existent
    $semestre = Semestre::find($semestre_id);
    $anneeScolaire = AnneeScolaire::find($annee_scolaire_id);
    
    if (!$semestre || !$anneeScolaire) {
        return response()->json([
            'success' => false,
            'message' => 'Semestre ou année scolaire invalide'
        ], 400);
    }

    // Récupérer les évaluations
    $evaluations = Evaluation::where('etudiant_id', $etudiant->id)
        ->where('semestre_id', $semestre_id)
        ->where('annee_scolaire_id', $annee_scolaire_id)
        ->with('matiere')
        ->get();

    // Calcul des statistiques
    $totalNotes = 0;
    $totalCoefficients = 0;
    $notesAvecCoefficients = [];
    
    foreach ($evaluations as $evaluation) {
        $notePonderee = $evaluation->note_finale * $evaluation->facteur;
        $totalNotes += $notePonderee;
        $totalCoefficients += $evaluation->facteur;
        $notesAvecCoefficients[] = [
            'matiere' => $evaluation->matiere->nom,
            'note' => $evaluation->note_finale,
            'coefficient' => $evaluation->facteur,
            'note_ponderee' => $notePonderee
        ];
    }
    
    $moyenneGenerale = $totalCoefficients > 0 ? $totalNotes / $totalCoefficients : 0;

    // Construction de la réponse
    $bulletin = [
        'etudiant' => [
            'id' => $etudiant->id,
            'nom_complet' => $etudiant->prenom . ' ' . $etudiant->nom,
            'classe' => $etudiant->classe->nom ?? 'Non spécifié'
        ],
        'periode' => [
            'semestre' => $semestre->libelle,
            'annee_scolaire' => $anneeScolaire->libelle
        ],
        'statistiques' => [
            'moyenne_generale' => round($moyenneGenerale, 2),
            'total_coefficients' => $totalCoefficients,
            'details_calcul' => $notesAvecCoefficients
        ],
        'evaluations' => $evaluations->map(function ($evaluation) {
            return [
                'matiere' => [
                    'id' => $evaluation->matiere->id,
                    'nom' => $evaluation->matiere->nom,
                    'coefficient' => $evaluation->matiere->coefficient ?? $evaluation->facteur
                ],
                'note' => $evaluation->note_finale,
                'appreciation' => $evaluation->appreciation ?? 'Non renseignée',
                'date_evaluation' => $evaluation->date_evaluation?->format('d/m/Y') ?? 'Non spécifiée'
            ];
        })->toArray()
    ];

    return response()->json([
        'success' => true,
        'data' => $bulletin
    ]);
}
public function voirBulletin($id)
{
    $etudiant = Etudiant::findOrFail($id);

    $evaluations = Evaluation::where('etudiant_id', $id)
        ->where('semestre_id', 1)
        ->where('annee_scolaire_id', 1)
        ->with('matiere')
        ->get();

    return view('etudiant.bulletin', compact('etudiant', 'evaluations'));
}
public function telechargerBulletinPDF($id)
{
    $etudiant = Etudiant::findOrFail($id);

    $evaluations = Evaluation::where('etudiant_id', $id)
        ->where('semestre_id', 1)
        ->where('annee_scolaire_id', 1)
        ->with('matiere')
        ->get();

    $pdf = Pdf::loadView('etudiant.bulletin_pdf', compact('etudiant', 'evaluations'));
    return $pdf->download('Bulletin_'.$etudiant->nom.'.pdf');
}


    // Récupération des notes d'un étudiant avec les informations de matière
    // public function getNotesEtudiant($etudiant_id, Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
    //         'semestre_id' => 'required|exists:semestres,id',
    //         'matiere_id' => 'nullable|exists:matieres,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $query = Evaluation::with(['matiere:id,nom', 'professeur:id,nom,specialite'])
    //         ->where('etudiant_id', $etudiant_id)
    //         ->where('annee_scolaire_id', $request->annee_scolaire_id)
    //         ->where('semestre_id', $request->semestre_id);

    //     if ($request->has('matiere_id')) {
    //         $query->where('matiere_id', $request->matiere_id);
    //     }

    //     $notes = $query->get([
    //         'id',
    //         'note1',
    //         'note2',
    //         'note3',
    //         'note4',
    //         'facteur',
    //         'note_finale',
    //         'remarque',
    //         'matiere_id',
    //         'professeur_id',
    //         'created_at',
    //         'updated_at'
    //     ]);

    //     return response()->json($notes);
    // }
}