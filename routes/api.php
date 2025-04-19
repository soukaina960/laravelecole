<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\SurveillantController;
use App\Http\Controllers\DirecteurController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\RetardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\NotificationController;

// ✅ Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);

// ✅ Étudiants
Route::apiResource('etudiants', EtudiantController::class);
Route::get('/etudiants/absence/{etudiantId}', [EtudiantController::class, 'getAbsences']);
Route::get('/etudiants/{id}/sanctions', [EtudiantController::class, 'getSanctions']);

// ✅ Surveillants
Route::apiResource('surveillants', SurveillantController::class);

// ✅ Directeurs
Route::apiResource('directeurs', DirecteurController::class);

// ✅ Professeurs
Route::prefix('professeurs')->group(function () {
    Route::get('/', [ProfesseurController::class, 'index']);
    Route::post('/', [ProfesseurController::class, 'store']);
    Route::get('/{id}', [ProfesseurController::class, 'show']);
    Route::put('/{id}', [ProfesseurController::class, 'update']);
    Route::delete('/{id}', [ProfesseurController::class, 'destroy']);
    Route::get('/{id}/update-total', [ProfesseurController::class, 'updateTotalForProfessor']);
    Route::post('/{id}/calculer-salaire', [ProfesseurController::class, 'calculerSalaire']);
});

// ✅ Classes
Route::apiResource('classes', ClasseController::class);
Route::get('/classes/{classe}/students', [ClasseController::class, 'students']);
Route::get('/classes/{classe}/total', [ClasseController::class, 'getTotalAbsencesForClasse']);
Route::post('/classes/{classe}/attendances', [ClasseController::class, 'manageAttendances']);

// ✅ Filières et Groupes
Route::apiResource('filieres', FiliereController::class);
Route::apiResource('groupes', GroupeController::class);

// ✅ Absences, Retards, Incidents
Route::apiResource('absences', AbsenceController::class);
Route::apiResource('retards', RetardController::class);
Route::apiResource('incidents', IncidentController::class);

// ✅ Statistiques
Route::get('/statistiques', [StatistiquesController::class, 'getStatistiques']);
Route::get('/statistiques/absences-par-classe', [StatistiquesController::class, 'getAbsencesByClasse']);
Route::get('/statistiques/retards-par-mois', [StatistiquesController::class, 'getRetardsByMonth']);
Route::get('/statistiques/incidents-par-type', [StatistiquesController::class, 'getIncidentsByType']);
Route::get('/statistiques/taux-presence', [StatistiquesController::class, 'getTauxPresence']);

// ✅ Séances (Seance)
Route::apiResource('seances', SeanceController::class);

// ✅ Cours & Devoirs
Route::apiResource('courses', CourseController::class);
Route::apiResource('assignments', AssignmentController::class);

// ✅ Notifications
Route::apiResource('notifications', NotificationController::class)->only(['index', 'store', 'show', 'destroy']);
Route::get('notifications/etudiant/{etudiant_id}', [NotificationController::class, 'getByEtudiant']);
Route::get('notifications/envoyeur/{user_id}', [NotificationController::class, 'getByEnvoyeur']);
