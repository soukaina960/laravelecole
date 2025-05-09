<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmploiTemps;
use App\Models\Professeur;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Professeur;
use Barryvdh\DomPDF\Facade\Pdf;


class EmploiTempsController extends Controller
{
    // Récupérer l'emploi du temps d'un professeur
    public function getByProfesseur($id)
    {
        return EmploiTemps::with(['classe', 'matiere', 'creneau'])
            ->where('professeur_id', $id)
            ->get();
    }



    // Exporter l'emploi du temps d'un professeur en PDF

    public function exportPdf($profId)
    {
        $professeur = Professeur::findOrFail($profId);
        $emplois = EmploiTemps::with(['matiere', 'classe', 'creneau'])
            ->where('professeur_id', $profId)
            ->get()
            ->groupBy('jour');


        $creneaux = $emplois->flatten()->pluck('creneau')->unique('heure_debut')->sortBy('heure_debut');

        $pdf = Pdf::loadView('pdf.emploi_prof', [
            'professeur' => $professeur,
            'emplois' => $emplois,
            'creneaux' => $creneaux,

        $creneaux = $emplois
            ->flatten()
            ->pluck('creneau')
            ->unique('heure_debut')
            ->sortBy('heure_debut');

        $pdf = Pdf::loadView('pdf.emploi_prof', [
            'professeur' => $professeur,
            'emplois'    => $emplois,
            'creneaux'   => $creneaux,

        ]);

        return $pdf->download('emploi_' . $professeur->nom . '.pdf');
    }

    // Récupérer les emplois du temps d'une classe
    public function index($classeId)
    {
        try {
            $emplois = EmploiTemps::with(['matiere', 'professeur', 'creneau'])
                ->where('classe_id', $classeId)
                ->get();

            return response()->json($emplois);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des données',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function recupurer()

    // Récupérer tous les emplois du temps
    public function recuperer()

    {
        return EmploiTemps::with(['classe', 'matiere', 'professeur', 'creneau'])->get();
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'salle' => 'required|string',
            'jour' => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'creneau_id' => 'required|exists:creneaux,id',
            'classe_id' => 'required|exists:classes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $emploi = EmploiTemps::findOrFail($id);
            $emploi->update($request->all());
            $emploi->load(['matiere', 'professeur', 'creneau']);
            return response()->json($emploi, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classe_id' => 'required|exists:classes,id',
            'jour' => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'creneau_id' => 'required|exists:creneaux,id',
            'matiere_id' => 'required|exists:matieres,id',

    // Mettre à jour un emploi du temps
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'classe_id'     => 'required|exists:classrooms,id',
            'jour'          => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'creneau_id'    => 'required|exists:creneaux,id',
            'matiere_id'    => 'required|exists:matieres,id',

            'professeur_id' => 'required|exists:professeurs,id',
            'salle'         => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $emploi = EmploiTemps::findOrFail($id);
            $emploi->update($request->all());
            $emploi->load(['matiere', 'professeur', 'creneau']);

            return response()->json($emploi, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Créer un nouvel emploi du temps
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classe_id'     => 'required|exists:classrooms,id',
            'jour'          => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'creneau_id'    => 'required|exists:creneaux,id',
            'matiere_id'    => 'required|exists:matieres,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'salle'         => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $emploi = EmploiTemps::create($request->all());
            return response()->json($emploi, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Supprimer un emploi du temps
    public function destroy($id)
    {
        try {
            EmploiTemps::findOrFail($id)->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
