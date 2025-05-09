<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\Evaluation;
use App\Models\Etudiant;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\ParentModel;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    // إنشاء bulletin جديد
    public function createBulletin(Request $request)
    {
        // جلب التقييمات ديال الطالب حسب السنة الدراسية
        $evaluations = Evaluation::where('etudiant_id', $request->etudiant_id)
                                 ->where('annee_scolaire_id', $request->annee_scolaire_id)
                                 ->get();
    
        if ($evaluations->count() > 0) {
            $totalPondere = 0;
            $totalFacteurs = 0;
    
            foreach ($evaluations as $evaluation) {
                if ($evaluation->note_finale !== null && $evaluation->facteur !== null) {
                    $totalPondere += $evaluation->note_finale * $evaluation->facteur;
                    $totalFacteurs += $evaluation->facteur;
                }
            }
    
            $moyenneGenerale = $totalFacteurs > 0 ? round($totalPondere / $totalFacteurs, 2) : 0;
    
            // إنشاء bulletin
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
            ], 200);
        }
    
        return response()->json([
            'message' => 'Aucune évaluation trouvée pour cet étudiant.',
        ], 404);
    }
    
    //get pour etudiant

    public function getBulletinByEtudiant($etudiantId)
{
    $bulletin = Bulletin::where('etudiant_id', $etudiantId)
                        ->where('est_traite', true)
                        ->latest()
                        ->first();

    if ($bulletin) {
        return response()->json($bulletin, 200);
    }

    return response()->json(['message' => 'Aucun bulletin traité trouvé.'], 404);
}



public function getBulletinForParent($parentId)
{
    // Récupérer l'étudiant associé au parent
    $etudiant = Etudiant::where('parent_id', $parentId)->first();

    if (!$etudiant) {
        return response()->json(['message' => 'Étudiant non trouvé pour ce parent.'], 404);
    }

    // Récupérer le bulletin traité de l'étudiant
    $bulletin = Bulletin::where('etudiant_id', $etudiant->id)
                        ->where('est_traite', true)
                        ->latest()
                        ->first();

    if (!$bulletin) {
        return response()->json(['message' => 'Aucun bulletin traité trouvé.'], 404);
    }

    return response()->json([
        'moyenne_generale' => $bulletin->moyenne_generale,
        'semestre_id' => $bulletin->semestre_id,
        'annee_scolaire_id' => $bulletin->annee_scolaire_id,
    ], 200);
}

public function generateBulletinPourParent($etudiant_id, $semestre_id, $annee_scolaire_id)
{
    $etudiant = Etudiant::findOrFail($etudiant_id);

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
        'semestre' => $semestre_id,
        'annee_scolaire' => $annee_scolaire_id,
        'moyenneGenerale' => $moyenneGenerale
    ];

    $pdf = PDF::loadView('bulletins.bulletin_pdf', $data);

    return $pdf->download('bulletin_' . $etudiant->nom . '.pdf');
}


}
