<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\ParentModel ;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    // ✅ Récupération des étudiants + leurs évaluations selon le professeur
    public function indexParClasseEtProfesseur($classeId, Request $request)
    {
        $professeurId = $request->query('professeur_id');

        $etudiants = Etudiant::where('classe_id', $classeId)->with(['evaluations' => function($query) use ($professeurId) {
            $query->where('professeur_id', $professeurId);
        }])->get();

        return response()->json($etudiants->map(function ($etudiant) {
            $evaluation = $etudiant->evaluations->first();
            return [
                'id' => $etudiant->id,
                'nom' => $etudiant->nom,
                'note1' => $evaluation->note1 ?? '',
                'note2' => $evaluation->note2 ?? '',
                'note3' => $evaluation->note3 ?? '',
                'note4' => $evaluation->note4 ?? '',
                'facteur' => $evaluation->facteur ?? 1,
                'note_finale' => $evaluation->note_finale ?? '',
                'remarque' => $evaluation->remarque ?? '',
                'semestre_id' => $evaluation->semestre_id ?? null,
            ];
        }));
    }

    // ✅ Enregistrement ou mise à jour des notes
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
          ->where('evaluations.etudiant_id', $etudiant_id)
          ->where('evaluations.annee_scolaire_id', $annee_scolaire_id)
          ->where('evaluations.semestre_id', $semestre_id)
          ->select('evaluations.*', 'professeurs.specialite')
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


}