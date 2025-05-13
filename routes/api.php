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
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\EmploiSurveillanceController;
use App\Http\Controllers\API\EmailParentController;
use App\Http\Controllers\AbsenceController;

use App\Http\Controllers\NotificationController;


use App\Http\Controllers\NotificationsController;


















use App\Http\Controllers\PaiementMensuelController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\PaiementController;






















use App\Http\Controllers\QuizController;























use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\FichierPedagogiqueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfigAttestationController;
use App\Http\Controllers\MatiereController;

use App\Http\Controllers\StatsController;
use App\Http\Controllers\RetardsController;
use App\Http\Controllers\DemandeAttestationController;
use App\Http\Controllers\SurveillantController;
use App\Http\Controllers\EtudiantProfesseurController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ReclamationController;

Route::get('/sanctions', [SanctionController::class, 'index']);
Route::get('/sanctions/create', [SanctionController::class, 'create']);
Route::post('/sanctions', [SanctionController::class, 'store']);

Route::get('/parents/user/{user_id}', [ParentController::class, 'getByUserId']);














use App\Http\Controllers\Api\EmploiTempsController;
// routes/api.php

use App\Http\Controllers\CreneauController;
use App\Http\Controllers\API\EvenementController;

use App\Http\Controllers\RetardPaiementController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ExamenController;
Route::post('/chatbot', [ChatBotController::class, 'handle']);


Route::get('/demandes-attestations', [DemandeAttestationController::class, 'index']);


Route::get('/admins', [AdminController::class, 'index']);
Route::get('/etudiants/retards', [RetardPaiementController::class, 'index']);
Route::post('/etudiants/{id}/envoyer-notification', [RetardPaiementController::class, 'envoyerNotification']);


Route::apiResource('evenements', EvenementController::class);
    Route::get('/evenements', [EvenementController::class, 'index']);
    Route::post('/evenements', [EvenementController::class, 'store']);
    Route::get('/evenements/{id}', [EvenementController::class, 'show']);
    Route::put('/evenements/{id}', [EvenementController::class, 'update']);
    Route::delete('/evenements/{id}', [EvenementController::class, 'destroy']);




Route::get('/emplois-temps', [EmploiTempsController::class, 'recupurer']);
Route::get('/emplois-temps/professeur/{id}/pdf', [EmploiTempsController::class, 'exportPdf']);
Route::post('paiement/reset-mois-precedent', [PaiementMensuelController::class, 'resetPaiementsMoisPrecedent']);
Route::delete('/professeurs/{professeurId}/etudiants/{etudiantId}', [ProfesseurController::class, 'destroyEtudiant']);
Route::get('/emplois-temps/professeur/{id}', [EmploiTempsController::class, 'getByProfesseur']);



// routes/api.php

Route::post('paiement/reset-mois-precedent', [PaiementMensuelController::class, 'resetPaiementsMoisPrecedent']);
Route::delete('/professeurs/{professeurId}/etudiants/{etudiantId}', [ProfesseurController::class, 'destroyEtudiant']);




    Route::get('/emplois-temps', [EmploiTempsController::class, 'recupurer']);
    Route::get('/emplois-temps/professeur/{id}/pdf', [EmploiTempsController::class, 'exportPdf']);
    Route::delete('/professeurs/{professeurId}/etudiants/{etudiantId}', [ProfesseurController::class, 'destroyEtudiant']);
    Route::get('/emplois-temps/professeur/{id}', [EmploiTempsController::class, 'getByProfesseur']);
    





// routes/api.php
Route::post('paiement/reset-mois-precedent', [PaiementMensuelController::class, 'resetPaiementsMoisPrecedent']);


Route::post('paiement/reset-mois-precedent', [PaiementMensuelController::class, 'resetPaiementsMoisPrecedent']);
Route::delete('/professeurs/{professeurId}/etudiants/{etudiantId}', [ProfesseurController::class, 'destroyEtudiant']);





Route::get('/creneaux', [CreneauController::class, 'index']);
Route::post('/creneaux', [CreneauController::class, 'store']);
Route::put('/creneaux/{id}', [CreneauController::class, 'update']);
Route::delete('/creneaux/{id}', [CreneauController::class, 'destroy']);

Route::get('/emplois-temps/{classeId}', [EmploiTempsController::class, 'index']);

Route::get('/absences/plus-de-15h', [AbsenceController::class, 'countEtudiantsAvecAbsenceSuperieureA15h']);

Route::put('/{id}', [EmploiTempsController::class, 'update']);

Route::put('/{id}', [EmploiTempsController::class, 'update']);



Route::put('/{id}', [EmploiTempsController::class, 'update']);


Route::put('/{id}', [EmploiTempsController::class, 'update']);



Route::prefix('emplois-temps')->group(function () {
    // GET /api/emplois-temps/{classeId} - Get schedule for a class
    Route::get('/{classeId}', [EmploiTempsController::class, 'index']);
    
    // POST /api/emplois-temps - Create new schedule entry
    Route::post('/', [EmploiTempsController::class, 'store']);
});






















Route::get('matieres', [MatiereController::class, 'index']);
Route::post('matieres', [MatiereController::class, 'store']);
Route::put('matieres/{id}', [MatiereController::class, 'update']);
Route::delete('matieres/{id}', [MatiereController::class, 'destroy']);




Route::get('/config-attestations', [ConfigAttestationController::class, 'index']);
Route::put('/config-attestations/{id}', [ConfigAttestationController::class, 'update']);
Route::post('/config-attestations', [ConfigAttestationController::class, 'store']);

Route::get('/etudiants/{id}/attestation-pdf', [StudentController::class, 'generateAttestation']);

Route::get('/charges', [ChargeController::class, 'index']);
Route::post('/charges', [ChargeController::class, 'store']);
Route::get('/charges/{charge}', [ChargeController::class, 'show']);
Route::put('/charges/{charge}', [ChargeController::class, 'update']);
Route::delete('/charges/{charge}', [ChargeController::class, 'destroy']);



Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/parents', [ParentController::class, 'index']);
Route::get('/parents/{id}', [ParentController::class, 'show']);
// api.php
Route::get('/parent/{parent}/paiements', [ParentController::class, 'paiementsDuParent']);

// CRUD routes
Route::apiResource('notifications', NotificationController::class)->only([
    'index', 'store', 'show', 'destroy'
]);
Route::get('notifications/etudiant/{etudiant_id}', [NotificationController::class, 'getByEtudiant']);
Route::get('notifications/envoyeur/{user_id}', [NotificationController::class, 'getByEnvoyeur']);
Route::post('/notifier-parent/{etudiantId}', [AbsenceController::class, 'notifyParent']);

Route::resource('surveillants', SurveillantController::class);
Route::resource('super_surveillants', SuperSurveillantController::class);
Route::get('/surveillants/{id}/absences', [SurveillantController::class, 'getAbsences']);
Route::get('/surveillants/{id}/retards', [SurveillantController::class, 'getRetards']);
Route::get('/surveillants/{id}/incidents', [SurveillantController::class, 'getIncidents']);
Route::get('/surveillants/{id}/emploi', [SurveillantController::class, 'getEmploi']);
Route::get('/surveillants/{id}/sanctions', [SurveillantController::class, 'getSanctions']); 

Route::get('/statistics-surveillant', [StatsController::class, 'getStatistics']);
Route::apiResource('notifications', NotificationController::class)->only([
    'index', 'store', 'show', 'destroy'
]);
Route::get('notifications/etudiant/{etudiant_id}', [NotificationController::class, 'getByEtudiant']);
Route::get('notifications/envoyeur/{user_id}', [NotificationController::class, 'getByEnvoyeur']);

Route::get('absences/etudiant/{etudiant_id}', [AbsenceController::class, 'getByEtudiant']);
Route::get('absences/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [AbsenceController::class, 'getByDateRange']);

Route::apiResource('absences', AbsenceController::class);
Route::get('/absences/parent/{parent_id}', [AbsenceController::class, 'getAbsencesByParentId']);
Route::get('/retards/parent/{parent_id}', [RetardsController::class, 'getRetardsByParentId']);
Route::get('/incidents/parent/{parent_id}', [IncidentController::class, 'getIncidentsByParentId']);


Route::get('/retards', [RetardsController::class, 'index']);
Route::post('/retards', [RetardsController::class, 'store']);
Route::get('retards/etudiant/{etudiant_id}', [RetardsController::class, 'getByEtudiant']);
Route::get('retards/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [RetardsController::class, 'getByDateRange']);
Route::apiResource('emplois', EmploiSurveillanceController::class);
Route::get('emplois/surveillant/{surveillant_id}', [EmploiSurveillanceController::class, 'getBySurveillant']);
Route::apiResource('incidents', IncidentController::class);
Route::post('/incidents', [IncidentController::class, 'store']);
Route::get('incidents/etudiant/{etudiant_id}', [IncidentController::class, 'getByEtudiant']);
Route::get('incidents/etudiant/{etudiant_id}/entre/{date_debut}/{date_fin}', [IncidentController::class, 'getByDateRange']);



Route::put('/parent/update/{id}', [ParentController::class, 'update']);

 





use Illuminate\Http\Request;





Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show']);
Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update']);
Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);


Route::get('/classrooms', [ClassroomController::class, 'index']);
Route::post('/classrooms', [ClassroomController::class, 'store']);
Route::get('/classrooms/{id}/students', [ClassroomController::class, 'students']);
Route::delete('/classrooms/{id}', [ClassroomController::class, 'destroy']);



Route::post('/reclamations', [ReclamationController::class, 'store']);
Route::get('/reclamations', [ReclamationController::class, 'index']);

Route::put('/classrooms/{id}', [ClassroomController::class, 'update']);
Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);



Route::get('/etudiants', [StudentController::class, 'index']);  
Route::post('/etudiants', [StudentController::class, 'store']);  
Route::get('/etudiants/{id}', [StudentController::class, 'show']);
Route::put('/etudiants/{id}', [StudentController::class, 'update']);
Route::delete('etudiants/{id}', [StudentController::class, 'destroy']);
Route::get('etudiants/{classeId}', [StudentController::class, 'getEtudiantsParClasse']);
Route::get('/etudiant-par-parent/{parentId}', [StudentController::class, 'getEtudiantByParent']);


//evaluation
Route::get('/evaluations/{classeId}', [EvaluationController::class, 'indexParClasseEtProfesseur']);
Route::post('/evaluations', [EvaluationController::class, 'store']);
Route::get('/notes', [EvaluationController::class, 'getNotesByParentAndSemestre']);
Route::get('/notes-etudiant/{etudiant_id}', [EvaluationController::class, 'getNotesEtudiant']);
Route::get('/notes-par-parent', [EvaluationController::class, 'getNotesByParentAndSemestre']);
// routes/api.php
Route::get('/notes-parent', [EvaluationController::class, 'getNotesByParent']);




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
   // routes/api.php
Route::get('/classe/{id}/filieres', [FiliereController::class, 'filieresForClasse']);

    Route::get('/etudiants/{etudiant_id}/parent-email', [ParentController::class, 'getParentEmail']);
Route::post('/send-message', [MessageController::class, 'send']);
//login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/data', [AuthController::class, 'store']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



Route::apiResource('charges', ChargeController::class);
Route::get('/rapport-pdf', [RapportController::class, 'exportPdf']);























Route::delete('/emplois_temps/{id}', [EmploiTempsController::class, 'destroy']);

Route::apiResource('charges', ChargeController::class);
Route::get('/rapport-pdf', [RapportController::class, 'exportPdf']);








Route::post('absences', [EtudiantProfesseurController::class, 'enregistrerAbsences']);


// routes/api.php
Route::get('annees_scolaires', [AnneeScolaireController::class, 'index']);

Route::get('/semestres', [SemestreController::class, 'index']);


Route::get('/paiements-mensuels', [PaiementMensuelController::class, 'index']);
Route::post('/paiements-mensuels', [PaiementMensuelController::class, 'store']);
Route::get('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'show']);
Route::put('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'update']);
Route::delete('/paiements-mensuels/{id}', [PaiementMensuelController::class, 'destroy']);



Route::prefix('fichiers-pedagogiques')->group(function () {















    Route::get('/', [FichierPedagogiqueController::class, 'index']);
    Route::post('/', [FichierPedagogiqueController::class, 'store']);
    Route::get('/{id}', [FichierPedagogiqueController::class, 'show']);
    Route::put('/{id}', [FichierPedagogiqueController::class, 'update']);
    Route::delete('/{id}', [FichierPedagogiqueController::class, 'destroy']);
    Route::get('/download/{id}', [FichierPedagogiqueController::class, 'download']);
});// routes/api.php
Route::middleware('auth:api')->group(function() {
    Route::get('/etudiant/info', [StudentController::class, 'getEtudiantInfo']);
});


// Dans ton API Laravel, quelque chose comme ça :


Route::get('/fichiers-etudiant', [FichierPedagogiqueController::class, 'fichiersPourEtudiant']);
Route::post('/absences', [AbsenceController::class, 'store']); // accessible sans auth

Route::get('etudiant/{id}/absences', [EtudiantController::class, 'getAbsences']);
Route::middleware('auth:api')->group(function () {
    Route::get('/etudiant/{id}/absences', [AbsenceController::class, 'getByEtudiant']);
});
// Route::middleware('auth:sanctum')->group(function () {
//     // Pour les étudiants
//     Route::get('/mes-absences', [AbsenceController::class, 'mesAbsences']);
    
//     // Pour les professeurs/admin
//     Route::prefix('absences')->group(function () {
//         Route::get('/etudiant/{etudiant}', [AbsenceController::class, 'getByStudent']);
//         Route::post('/', [AbsenceController::class, 'store']);
//         Route::put('/{id}', [AbsenceController::class, 'update']);
//         Route::delete('/{id}', [AbsenceController::class, 'destroy']);
//     });
// });Route::middleware('auth:api')->get('/etudiants/{id}', [EtudiantController::class, 'show']);

Route::get('/parent-dashboard/{parent_id}', [ParentDashboardController::class, 'getDashboardData']);
Route::get('/parent-dashboard/{parent_id}', [ParentDashboardController::class, 'getDashboardData']);
Route::get('/paiement/receipt/{parent_id}/{mois}', [PaiementMensuelController::class, 'generateReceipt']);
Route::get('paiements/parent/{parent_id}/{mois}', [PaiementMensuelController::class, 'getPaiementsByMois']);

    // Pour les professeurs/admin
    Route::prefix('absences')->group(function () {
        Route::get('/etudiant/{etudiant}', [AbsenceController::class, 'getByStudent']);
        Route::post('/', [AbsenceController::class, 'store']);
        Route::put('/{id}', [AbsenceController::class, 'update']);
        Route::delete('/{id}', [AbsenceController::class, 'destroy']);
    });


















;Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);






;Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);














Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);







Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);










Route::middleware('auth:sanctum')->group(function () {
    Route::get('/paiements-mensuels/{etudiantId}', [PaiementMensuelController::class, 'listePaiements']);  // Changé ici
    Route::post('/paiements-mensuels/{paiementId}/payer', [PaiementMensuelController::class, 'payer']);
});
Route::group(['prefix' => 'professeur'], function () {
    Route::get('/{professeur}/panier-detail', [ProfesseurController::class, 'panierDetail']);
    Route::get('/{professeur}/mois-paiements', [ProfesseurController::class, 'getMoisPaiements']);
});

Route::get('/annees-scolaires', [AnneeScolaireController::class, 'index']);
Route::get('/annees/{annee}/semestres', [AnneeScolaireController::class, 'semestres']);
Route::get('/classes', [ClasseController::class, 'index']);
Route::get('/classes/{classe}/filieres', [ClasseController::class, 'getFilieresByClasse']);

Route::get('/professeurs/{professeur}/classes/{classe}/matieres', [ProfesseurController::class, 'matieresSansFiliere']);
Route::get('/professeurs/{professeur}/classes/{classe}/filieres/{filiere}/matieres', [ProfesseurController::class, 'matieresAvecFiliere']);
Route::get('/matieres-par-prof-classe', [ProfesseurController::class, 'getMatieres']);


Route::get('professeur/{professeurId}/paiements/{mois}', [PaiementMensuelController::class, 'getPaiements']);

Route::get('/professeurs/{id}', [ProfesseurController::class, 'affichierinfo']);
// routes/api.php



Route::prefix('demandes-attestations')->group(function () {
    Route::get('/', [DemandeAttestationController::class, 'index']); // Admin
    Route::post('/', [DemandeAttestationController::class, 'store']); // Étudiant
    Route::patch('/{id}/traiter', [DemandeAttestationController::class, 'marquerCommeTraitee']); // Admin
    Route::get('/etudiant/{id}', [DemandeAttestationController::class, 'demandesEtudiant']); // Étudiant
});


Route::put('/reclamations/{id}', [ReclamationController::class, 'update']);

























Route::get('/attestations/{id}/download', [DemandeAttestationController::class, 'download']);
Route::get('/demandes-non-traitees', [DemandeAttestationController::class, 'demandesNonTraitees']);
Route::post('/traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);
Route::get('/demandes-non-traitees', [DemandeAttestationController::class, 'demandesNonTraitees']);
Route::post('/traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);
Route::get('demandes-non-traitees', [DemandeAttestationController::class, 'getDemandesNonTraitees']);

Route::post('traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);


Route::get('/absents-critiques', [AbsenceController::class, 'getAbsentsCritiques']);
Route::post('traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);Route::get('/absents-critiques', [AbsenceController::class, 'getAbsentsCritiques']);




Route::get('/absents-critiques', [AbsenceController::class, 'getAbsentsCritiques']);

Route::post('traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);Route::get('/absents-critiques', [AbsenceController::class, 'getAbsentsCritiques']);


Route::get('/retards-paiement', [PaiementMensuelController::class, 'getCountEtudiantsSansPaiement']);















Route::post('/examens', [ExamenController::class, 'store']);
Route::get('/examens/{id}', [ExamenController::class, 'show']);
// Pour les étudiants
Route::get('/mon-emploi-examens', [ExamenController::class, 'emploiExamensEtudiant'])
    ->middleware(['auth', 'etudiant'])
    ->name('examens.etudiant');

// Ou pour une API (si vous utilisez React)
Route::get('/api/etudiant/examens', [ExamenController::class, 'getExamensEtudiant'])




    ->middleware(['auth:api', 'etudiant']);
    Route::get('/etudiants/{Classroom}/examens', [ExamenController::class, 'getExamensEtudiant']);










    Route::prefix('quizzes')->group(function () {
        Route::post('/', [QuizController::class, 'store']); // Création
        Route::get('/{quiz}', [QuizController::class, 'show']); // Mise à jour
    });
    // Quiz (étudiant)
    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('quizzes', [QuizController::class, 'indexForStudent']);
        Route::post('quizzes/{quiz}/submit', [QuizController::class, 'submit']);
        Route::get('results', [QuizController::class, 'studentResults']);
    });
    Route::get('/quizzes', [QuizController::class, 'index']);
Route::post('/quizzes', [QuizController::class, 'store']);
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::delete('/quizzes/{id}', [QuizController::class, 'destroy']);
// Temporairement dans routes/api.php


Route::post('demandes-attestations/{id}/traiter', [DemandeAttestationController::class, 'marquerCommeTraitee']);



