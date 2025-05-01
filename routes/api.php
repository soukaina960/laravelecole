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
use App\Http\Controllers\NotificationsController;





use App\Http\Controllers\PaiementMensuelController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\PaiementController;
















use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\FichierPedagogiqueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfigAttestationController;
use App\Http\Controllers\MatiereController;

use App\Http\Controllers\DemandeAttestationController;
use App\Http\Controllers\Api\EmploiTempsController;
// routes/api.php

use App\Http\Controllers\CreneauController;
use App\Http\Controllers\API\EvenementController;

use App\Http\Controllers\RetardPaiementController;
use App\Http\Controllers\ChatBotController;

Route::post('/chatbot', [ChatBotController::class, 'handle']);


Route::get('/demandes-attestations', [DemandeAttestationController::class, 'index']);

Route::post('/demandes-attestations/{id}/traiter', [DemandeAttestationController::class, 'traiterDemande']);

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

Route::get('/creneaux', [CreneauController::class, 'index']);
Route::post('/creneaux', [CreneauController::class, 'store']);
Route::put('/creneaux/{id}', [CreneauController::class, 'update']);
Route::delete('/creneaux/{id}', [CreneauController::class, 'destroy']);

Route::get('/emplois-temps/{classeId}', [EmploiTempsController::class, 'index']);

Route::get('/absences/plus-de-15h', [AbsenceController::class, 'countEtudiantsAvecAbsenceSuperieureA15h']);
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




Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show']);
Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update']);
Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);


Route::get('/classrooms', [ClassroomController::class, 'index']);
Route::post('/classrooms', [ClassroomController::class, 'store']);
Route::delete('/classrooms/{id}', [ClassroomController::class, 'destroy']);
Route::put('/classrooms/{id}', [ClassroomController::class, 'update']);
Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);


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

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::apiResource('charges', ChargeController::class);
Route::get('/rapport-pdf', [RapportController::class, 'exportPdf']);



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
Route::prefix('fichiers-pedagogiques')->group(function () {

Route::prefix('fichiers-pedagogiques')->group(function () {


Route::prefix('fichiers-pedagogiques')->group(function () {

Route::prefix('fichiers')->group(function () {

    Route::get('/', [FichierPedagogiqueController::class, 'index']);
    Route::post('/', [FichierPedagogiqueController::class, 'store']);
    Route::get('/{id}', [FichierPedagogiqueController::class, 'show']);
    Route::put('/{id}', [FichierPedagogiqueController::class, 'update']);
    Route::delete('/{id}', [FichierPedagogiqueController::class, 'destroy']);
    Route::get('/download/{id}', [FichierPedagogiqueController::class, 'download']);
});// routes/api.php
Route::middleware('auth:api')->group(function() {
    Route::get('/etudiant/info', [EtudiantController::class, 'getEtudiantInfo']);
});
Route::get('/notes-etudiant/{etudiant_id}', [EvaluationController::class, 'getNotesEtudiant']);
Route::get('/fichiers-etudiant', [FichierPedagogiqueController::class, 'fichiersPourEtudiant']);

Route::get('etudiant/{id}/absences', [EtudiantController::class, 'getAbsences']);
Route::middleware('auth:api')->group(function () {
    Route::get('/etudiant/{id}/absences', [AbsenceController::class, 'getByEtudiant']);
});
Route::middleware('auth:sanctum')->group(function () {
    // Pour les étudiants
    Route::get('/mes-absences', [AbsenceController::class, 'mesAbsences']);
    
    // Pour les professeurs/admin
    Route::prefix('absences')->group(function () {
        Route::get('/etudiant/{etudiant}', [AbsenceController::class, 'getByStudent']);
        Route::post('/', [AbsenceController::class, 'store']);
        Route::put('/{id}', [AbsenceController::class, 'update']);
        Route::delete('/{id}', [AbsenceController::class, 'destroy']);
    });

});Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);

});


});Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);

});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/paiements-mensuels/{etudiantId}', [PaiementMensuelController::class, 'listePaiements']);  // Changé ici
    Route::post('/paiements-mensuels/{paiementId}/payer', [PaiementMensuelController::class, 'payer']);
});
Route::group(['prefix' => 'professeur'], function () {
    Route::get('/{professeur}/panier-detail', [ProfesseurController::class, 'panierDetail']);
    Route::get('/{professeur}/mois-paiements', [ProfesseurController::class, 'getMoisPaiements']);
});

//Route::get('/annees-scolaires', [AnneeScolaireController::class, 'index']);
Route::get('/annees/{annee}/semestres', [AnneeScolaireController::class, 'semestres']);
Route::get('/classes', [ClasseController::class, 'index']);
Route::get('/classes/{classe}/filieres', [ClasseController::class, 'getFilieresByClasse']);

Route::get('/professeurs/{professeur}/classes/{classe}/matieres', [ProfesseurController::class, 'matieresSansFiliere']);
Route::get('/professeurs/{professeur}/classes/{classe}/filieres/{filiere}/matieres', [ProfesseurController::class, 'matieresAvecFiliere']);

Route::get('professeur/{professeurId}/paiements/{mois}', [PaiementMensuelController::class, 'getPaiements']);


Route::get('/professeurs/{id}', [ProfesseurController::class, 'affichierinfo']);
// routes/api.php



Route::prefix('demandes-attestations')->group(function () {
    Route::get('/', [DemandeAttestationController::class, 'index']); // Admin
    Route::post('/', [DemandeAttestationController::class, 'store']); // Étudiant
    Route::patch('/{id}/traiter', [DemandeAttestationController::class, 'marquerCommeTraitee']); // Admin
    Route::get('/etudiant/{id}', [DemandeAttestationController::class, 'demandesEtudiant']); // Étudiant
});

Route::get('/attestations/{id}/download', [DemandeAttestationController::class, 'download']);
Route::get('/demandes-non-traitees', [DemandeAttestationController::class, 'demandesNonTraitees']);
Route::post('/traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);
Route::get('/demandes-non-traitees', [DemandeAttestationController::class, 'demandesNonTraitees']);
Route::post('/traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);
Route::get('demandes-non-traitees', [DemandeAttestationController::class, 'getDemandesNonTraitees']);
Route::post('traiter-demande/{id}', [DemandeAttestationController::class, 'traiterDemande']);

use App\Http\Controllers\ExamenController;

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
