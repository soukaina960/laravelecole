<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Attendance;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\Professeur;
use App\Models\Utilisateur;

use App\Models\AnneeScolaire;       
class ClasseController extends Controller
{
    // PUT /api/classrooms/{id}


   // Dans ClasseController.php
   public function getEtudiants($classeId)
{
    try {
        $classe = Classroom::findOrFail($classeId);
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
   public function index(){
 
    $classrooms = Classroom::with('filiere')->get();
return response()->json($classrooms);



   }
  
   public function getFilieresByClasse($classeId)
   {
       try {
           $classe = Classroom::findOrFail($classeId);
           
           if (strtolower($classe->niveau) !== 'secondaire') {
               return response()->json([
                   'filieres' => [],
                   'message' => 'Cette classe n\'est pas un lycée.'
               ]);
           }
           
           // Récupérer la filière associée à la classe
           $filiere = $classe->filiere;
           
           if (!$filiere) {
               return response()->json([
                   'filieres' => [],
                   'message' => 'Aucune filière trouvée pour cette classe'
               ]);
           }
           
           return response()->json([
               'filieres' => [$filiere] // Retourne toujours un tableau
           ]);
           
       } catch (\Exception $e) {
           return response()->json([
               'filieres' => [],
               'message' => 'Erreur lors de la récupération des filières',
               'error' => $e->getMessage()
           ], 500);
       }
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