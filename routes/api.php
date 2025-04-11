<?php
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\API\RetardController;
use App\Http\Controllers\API\IncidentController;
use App\Http\Controllers\API\EmploiSurveillanceController;
use App\Http\Controllers\API\EmailParentController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaiementMensuelController;

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\SemestreController;


// CRUD routes
Route::apiResource('notifications', NotificationController::class)->only([
    'index', 'store', 'show', 'destroy'
]);
Route::get('notifications/etudiant/{etudiant_id}', [NotificationController::class, 'getByEtudiant']);
Route::get('notifications/envoyeur/{user_id}', [NotificationController::class, 'getByEnvoyeur']);


// Routes API standards
Route::apiResource('absences', AbsenceController::class);
Route::get('absences/etudiant/{etudiant_id}', [AbsenceController::class, 'getByEtudiant']);
Route::get('absences/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [AbsenceController::class, 'getByDateRange']);
Route::apiResource('retards', RetardController::class);
Route::get('retards/etudiant/{etudiant_id}', [RetardController::class, 'getByEtudiant']);
Route::get('retards/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [RetardController::class, 'getByDateRange']);
Route::apiResource('emplois', EmploiSurveillanceController::class);
Route::get('emplois/surveillant/{surveillant_id}', [EmploiSurveillanceController::class, 'getBySurveillant']);
Route::apiResource('incidents', IncidentController::class);
Route::get('incidents/etudiant/{etudiant_id}', [IncidentController::class, 'getByEtudiant']);
Route::get('incidents/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [IncidentController::class, 'getByDateRange']);







use Illuminate\Http\Request;
use App\Http\Controllers\EtudiantProfesseurController;
use App\Http\Controllers\EmploiTempsController;




Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show']);
Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update']);
Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);


Route::get('/classrooms', [ClassroomController::class, 'index']);
Route::post('/classrooms', [ClassroomController::class, 'store']);
Route::delete('/classrooms/{id}', [ClassroomController::class, 'destroy']);



Route::get('/etudiants', [StudentController::class, 'index']);  
Route::post('/etudiants', [StudentController::class, 'store']);  
Route::get('/etudiants/{id}', [StudentController::class, 'show']);
Route::put('/etudiants/{id}', [StudentController::class, 'update']);
Route::delete('etudiants/{id}', [StudentController::class, 'destroy']);
Route::get('etudiants/{classeId}', [StudentController::class, 'getEtudiantsParClasse']);


Route::get('/etudiant_professeur/{classe_id}', [EtudiantProfesseurController::class, 'getEtudiantsProfesseurs']);

// routes/api.php
Route::post('professeurs/{id}/calculer-salaire', [ProfesseurController::class, 'calculerSalaire']);
Route::get('/professeurs/{id}/update-total', [ProfesseurController::class, 'updateTotalForProfessor']);

Route::post('utilisateurs', [UtilisateurController::class, 'store']);
Route::get('/calculer-salaire-professeur/{id}', [ProfesseurController::class, 'calculerSalaire']);
Route::get('/professeurs', [ProfesseurController::class, 'index']); // Récupérer tous les professeurs
Route::get('/professeurs/{id}', [ProfesseurController::class, 'show']);
Route::post('/professeurs', [ProfesseurController::class, 'store']); // Ajouter un professeur
Route::put('/professeurs/{id}', [ProfesseurController::class, 'update']); // Modifier un professeur
Route::delete('/professeurs/{id}', [ProfesseurController::class, 'destroy']); // Supprimer un professeur
// Gestion des présences
// Courses routes
Route::get('/courses', [CourseController::class, 'index']); // Lister tous les cours
Route::post('/courses', [CourseController::class, 'store']); // Créer un nouveau cours
Route::get('/courses/{course}', [CourseController::class, 'show']); // Afficher un cours spécifique
Route::put('/courses/{course}', [CourseController::class, 'update']); // Mettre à jour un cours
Route::delete('/courses/{course}', [CourseController::class, 'destroy']); // Supprimer un cours

// Communication
Route::get('/courses/{course}/students', [CourseController::class, 'getStudents']);
Route::get('/courses/{course}/parents', [CourseController::class, 'getParents']);
Route::post('/messages', [MessageController::class, 'sendBulkMessage']);

Route::get('/assignments', [AssignmentController::class, 'index']);
Route::post('/assignments', [AssignmentController::class, 'store']);
Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'getSubmissions']);
Route::post('/assignments/{assignment}/submissions', [AssignmentController::class, 'submitGrade']);
Route::get('/filieres', [FiliereController::class, 'index']);
Route::post('/filieres', [FiliereController::class, 'store']);
Route::get('/filieres/{filiereId}/classes', [FiliereController::class, 'getClasses']);
Route::put('/filieres/{id}', [FiliereController::class, 'update']);
Route::delete('/filieres/{id}', [FiliereController::class, 'destroy']);
// Routes pour les classes
Route::get('/classes/{classeId}/students', [ClasseController::class, 'getStudents']);
Route::get('/classes/{classeId}/attendances', [ClasseController::class, 'getAttendances']);
Route::post('/classes/{classeId}/attendances', [ClasseController::class, 'manageAttendances']);
Route::get('/classes/{classe}/etudiants', [ClasseController::class, 'getEtudiants']);
// Wrong (if manageAttendances doesn't exist):
    Route::post('/classes/{classe}/attendances', [ClasseController::class, 'manageAttendances']);

    // Correct (use the existing method name):
    Route::post('/classes/{classe}/attendances', [ClasseController::class, 'storeAttendances']);
   
    Route::get('/etudiants/{etudiant_id}/parent-email', [ParentController::class, 'getParentEmail']);
Route::post('/send-message', [MessageController::class, 'send']);
//login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/emplois_temps', [EmploiTempsController::class, 'index']);
Route::post('/emplois_temps', [EmploiTempsController::class, 'store']);
Route::delete('/emplois_temps/{id}', [EmploiTempsController::class, 'destroy']);

Route::apiResource('charges', ChargeController::class);
Route::get('/rapport-pdf', [RapportController::class, 'exportPdf']);
Route::post('absences', [EtudiantProfesseurController::class, 'enregistrerAbsences']);
Route::get('/evaluations/{classeId}', [EvaluationController::class, 'indexParClasseEtProfesseur']);
Route::post('/evaluations', [EvaluationController::class, 'store']);

// routes/api.php
Route::get('annees_scolaires', [AnneeScolaireController::class, 'index']);

Route::get('/semestres', [SemestreController::class, 'index']);


Route::get('/paiements-mensuels', [PaiementMensuelController::class, 'index']);
Route::post('/paiements-mensuels', [PaiementMensuelController::class, 'store']);
Route::get('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'show']);
Route::put('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'update']);
Route::delete('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'destroy']);

