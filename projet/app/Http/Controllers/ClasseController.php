<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Etudiant;

class ClassroomController extends Controller
{
   // Dans ClassroomController.php
   public function getEtudiants($classeId)
{
    try {
        $classe = Classe::findOrFail($classeId);
        $etudiants = $classe->etudiants;
        
        return response()->json([
            'success' => true,
            'data' => $etudiants
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de chargement',
            'error' => $e->getMessage()
        ], 500);
    }
}
  
   public function getAttendances($classeId)
   {
       $attendances = Attendance::with('etudiant')
           ->where('classe_id', $classeId)
           ->get()
           ->map(function ($attendance) {
               return [
                   'etudiant_id' => $attendance->etudiant_id,
                   'status' => $attendance->status,
                   'etudiant_nom' => $attendance->etudiant->nom,
                   'etudiant_prenom' => $attendance->etudiant->prenom
               ];
           });
       
       return response()->json($attendances);
   }
   public function storeAttendances(Request $request, $classeId)
{
    $validated = $request->validate([
        'attendances' => 'required|array|min:1',
        'attendances.*.etudiant_id' => 'required|exists:etudiants,id',
        'attendances.*.classe_id' => 'required|exists:classes,id',
        'attendances.*.course_id' => 'nullable|exists:courses,id', // Si la table courses existe
        'attendances.*.date' => 'required|date',
        'attendances.*.status' => 'required|in:present,absent',
        'attendances.*.notes' => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();
        
        foreach ($validated['attendances'] as $attendance) {
            Attendance::updateOrCreate(
                [
                    'classe_id' => $attendance['classe_id'],
                    'etudiant_id' => $attendance['etudiant_id'],
                    'date' => $attendance['date']
                ],
                [
                    'course_id' => $attendance['course_id'] ?? null,
                    'status' => $attendance['status'],
                    'notes' => $attendance['notes'] ?? null
                ]
            );
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Absences enregistrées'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function manageAttendances(Request $request, $classeId)
{
    // Your implementation here
    $validated = $request->validate([
        'attendances' => 'required|array',
        'attendances.*.etudiant_id' => 'required|exists:etudiants,id',
        'attendances.*.status' => 'required|in:present,absent'
    ]);

    try {
        foreach ($validated['attendances'] as $attendance) {
            Attendance::updateOrCreate(
                [
                    'classe_id' => $classeId,
                    'etudiant_id' => $attendance['etudiant_id'],
                    'date' => now()->format('Y-m-d')
                ],
                ['status' => $attendance['status']]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Présences enregistrées avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'enregistrement',
            'error' => $e->getMessage()
        ], 500);
    }
}
}