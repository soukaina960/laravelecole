<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Retard;
use App\Models\Absence;
use App\Models\Evaluation;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentDashboardController extends Controller
{
    public function getDashboardData($parent_id)
    {
        // 1. Récupérer l’étudiant lié à ce parent
        $etudiant = Etudiant::where('parent_id', $parent_id)->first();

        if (!$etudiant) {
            return response()->json(['message' => 'Aucun étudiant trouvé.'], 404);
        }

        $etudiantId = $etudiant->id;

        // 2. Rassembler les stats
        $moyenne = Evaluation::where('etudiant_id', $etudiantId)
                             ->avg(DB::raw('(note1 + note2 + note3 + note4) / 4')) ?? 0;
        $retards = Retard::where('etudiant_id', $etudiantId)->count();
        $notesCount = Evaluation::where('etudiant_id', $etudiantId)->count();
        $incidents = Incident::where('etudiant_id', $etudiantId)->count();

        // 3. Derniers événements
        $derniersNotes = Evaluation::where('etudiant_id', $etudiantId)->latest('created_at')->take(5)->get();
        $derniersRetards = Retard::where('etudiant_id', $etudiantId)->latest('created_at')->take(2)->get();
        $derniersIncidents = Incident::where('etudiant_id', $etudiantId)->latest('created_at')->take(2)->get();

        return response()->json([
            'stats' => [
                'moyenne' => round($moyenne, 2),
                'retards' => $retards,
                'notes' => $notesCount,
                'incidents' => $incidents,
            ],
            'derniers' => [
                'notes' => $derniersNotes,
                'retards' => $derniersRetards,
                'incidents' => $derniersIncidents,
            ]
        ]);
    }
}
