<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Evaluation;
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
        // Pour debug : afficher dans le log Laravel
        Log::info($request->all());

        // Validation complète
        $validated = $request->validate([
            'professeur_id' => 'required|exists:professeurs,id',
            'notes' => 'required|array',
            'notes.*.etudiant_id' => 'required|exists:etudiants,id',
            'notes.*.annee_scolaire_id' => 'nullable|exists:annees_scolaires,id',
            'notes.*.semestre_id' => 'nullable|exists:semestres,id', // 💡 ajout ici
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
                    'semestre_id' => $note['semestre_id'] ?? null, // 💡 clé d'identification si besoin
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
  

}