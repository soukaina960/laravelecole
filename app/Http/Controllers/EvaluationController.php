<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    // Enregistrement ou mise à jour des notes
    public function store(Request $request)
    {
        Log::info('Données reçues:', $request->all());

        $validator = Validator::make($request->all(), [
            'classe_id' => 'required|exists:classrooms,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'semestre_id' => 'required|exists:semestres,id',
            'notes' => 'required|array|min:1',
            'notes.*.etudiant_id' => 'required|exists:etudiants,id',
            'notes.*.note1' => 'nullable|numeric|min:0|max:20',
            'notes.*.note2' => 'nullable|numeric|min:0|max:20',
            'notes.*.note3' => 'nullable|numeric|min:0|max:20',
            'notes.*.note4' => 'nullable|numeric|min:0|max:20',
            'notes.*.facteur' => 'required|numeric|min:0.1|max:5',
            'notes.*.note_finale' => 'required|numeric|min:0|max:20',
            'notes.*.remarque' => 'nullable|string|max:255',
            'notes.*.annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'notes.*.semestre_id' => 'required|exists:semestres,id',
            'notes.*.matiere_id' => 'required|exists:matieres,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->notes as $note) {
                Evaluation::updateOrCreate(
                    [
                        'etudiant_id' => $note['etudiant_id'],
                        'professeur_id' => $request->professeur_id,
                        'matiere_id' => $note['matiere_id'],
                        'annee_scolaire_id' => $note['annee_scolaire_id'],
                        'semestre_id' => $note['semestre_id'],
                    ],
                    [
                        'classe_id' => $request->classe_id,
                        'note1' => $note['note1'] ?? null,
                        'note2' => $note['note2'] ?? null,
                        'note3' => $note['note3'] ?? null,
                        'note4' => $note['note4'] ?? null,
                        'facteur' => $note['facteur'],
                        'note_finale' => $note['note_finale'],
                        'remarque' => $note['remarque'] ?? null,
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'message' => 'Notes enregistrées avec succès',
                'data' => $request->all()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur enregistrement: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Récupération des notes d'un étudiant avec les informations de matière
    public function getNotesEtudiant($etudiant_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'semestre_id' => 'required|exists:semestres,id',
            'matiere_id' => 'nullable|exists:matieres,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Evaluation::with(['matiere:id,nom', 'professeur:id,nom,specialite'])
            ->where('etudiant_id', $etudiant_id)
            ->where('annee_scolaire_id', $request->annee_scolaire_id)
            ->where('semestre_id', $request->semestre_id);

        if ($request->has('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        $notes = $query->get([
            'id',
            'note1',
            'note2',
            'note3',
            'note4',
            'facteur',
            'note_finale',
            'remarque',
            'matiere_id',
            'professeur_id',
            'created_at',
            'updated_at'
        ]);

        return response()->json($notes);
    }
}