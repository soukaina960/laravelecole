<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Utilisateur;
use App\Models\Admin;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Surveillant;
use App\Models\ParentModel;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;




use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        
        if ($role) {
            return Utilisateur::where('role', $role)->get();
        }
    
        return Utilisateur::all(); // Récupère tous les utilisateurs si aucun filtre n'est appliqué
    }
    public function store(Request $request)
    {
        try {
            $password = Str::random(10);
            $matricule = Str::upper(Str::random(2) . rand(1000, 9999));

            $validatedData = $request->validate([
                'telephone' => 'nullable|string',

                'nom' => ['required', 'string', 'regex:/^[\pL\s\-]+$/u'],
                'email' => 'required|email|unique:utilisateurs',
                'role' => 'required|in:admin,professeur,surveillant,étudiant,parent',
                'adresse' => 'nullable|string',
            ]);

            $chemin = 'default_image.png';
            if ($request->hasFile('photo_profil')) {
                $chemin = $request->file('photo_profil')->store('photos', 'public');
            }

            $utilisateur = Utilisateur::create([
                'nom' => $validatedData['nom'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'telephone' => $validatedData['telephone'] ?? null,
                'adresse' => $validatedData['adresse'] ?? null,
                'photo_profil' => $chemin,
                'matricule' => $matricule,
                'mot_de_passe' => Hash::make($password),
                  'password_changed' => false, // ← IMPORTANT: initialisé à false
            ]);

            // ENVOI EMAIL
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'aitouhlalfarah18@gmail.com';
                $mail->Password = 'csfbjnjcukhhtbvh'; // Remplace par ton mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function ($str, $level) {
                    Log::info("PHPMailer: $str");
                };

                $mail->setFrom('aitouhlalfarah18@gmail.com', 'Administration');
                $mail->addAddress($utilisateur->email, $utilisateur->nom);
                $mail->Subject = 'Votre compte a été créé';
                $mail->Body = "Bonjour {$utilisateur->nom},\n\nVotre compte a été créé avec succès.\nVoici votre mot de passe : {$password} et votre matricule  :{$utilisateur->matricule}   \n\nMerci de le changer après connexion.";

                if (!$mail->send()) {
                    Log::error('Email non envoyé: ' . $mail->ErrorInfo);
                } else {
                    Log::info('Email envoyé à ' . $utilisateur->email);
                }
            } catch (\Exception $e) {
                Log::error('Erreur envoi email: ' . $e->getMessage());
            }

            // CRÉATION PAR RÔLE
            switch ($utilisateur->role) {
                case 'admin':
                    Admin::create([
                        'user_id' => $utilisateur->id,
                        'name' => $validatedData['nom'],
                        'email' => $validatedData['email'],
                        'password' => Hash::make($password),
                    ]);
                    break;

                case 'surveillant':
                    Surveillant::create([
                        'user_id' => $utilisateur->id,
                        'nom' => $validatedData['nom'],
                        'email' => $validatedData['email'],
                        'password' => Hash::make($password),
                    ]);
                    break;

                case 'étudiant':
                    $studentData = $request->validate([

                     'prenom' => 'required|string|alpha',
                        'date_naissance' => 'required|date',
                        'sexe' => 'required|in:M,F',
                        'montant_a_payer' => 'nullable|numeric',
                        'classe_id' => 'nullable|exists:classrooms,id',
                        'professeurs' => 'array|nullable',
                        'professeurs.*' => 'exists:professeurs,id',
                        'origine' => 'nullable|string',
                        'parent_id' => 'nullable|exists:parents,id',
                    ]);

                    $etudiant = Etudiant::create([
                        'utilisateur_id' => $utilisateur->id,
                        'nom' => $validatedData['nom'],
                        'prenom' => $studentData['prenom'],
                        'matricule' => $matricule,
                        'email' => $validatedData['email'],
                        'origine' => $studentData['origine'] ?? null,
                        'parent_id' => $studentData['parent_id'] ?? null,
                        'date_naissance' => $studentData['date_naissance'],
                        'sexe' => $studentData['sexe'],
                        'adresse' => $validatedData['adresse'] ?? null,
                        'photo_profil' => $chemin,
                        'montant_a_payer' => $studentData['montant_a_payer'] ?? 0,
                        'classe_id' => $studentData['classe_id'] ?? null,
                    ]);

                    if ($request->has('professeurs')) {
                        $etudiant->professeurs()->sync($request->professeurs);
                    }
                    break;

                case 'professeur':
                    $profData = $request->validate([
                        'niveau_enseignement' => 'nullable|string',
                        'diplome' => 'nullable|string',
                        'date_embauche' => 'nullable|date',
                        'matieres_classes' => 'array|required',
                        'matieres_classes.*.matiere_id' => 'required|exists:matieres,id',
                        'matieres_classes.*.classe_id' => 'required|exists:classrooms,id',
                    ]);

                    $professeur = Professeur::create([
                        'user_id' => $utilisateur->id,
                        'nom' => $utilisateur->nom,
                        'email' => $utilisateur->email,
                        'niveau_enseignement' => $profData['niveau_enseignement'],
                        'diplome' => $profData['diplome'],
                        'date_embauche' => $profData['date_embauche'],
                    ]);

                    foreach ($profData['matieres_classes'] as $item) {
                        DB::table('prof_matiere_classe')->insert([
                            'professeur_id' => $professeur->id,
                            'classe_id' => $item['classe_id'],
                            'matiere_id' => $item['matiere_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;

                case 'parent':
                    $parentData = $request->validate([
                        'prenom' => 'required|string',
                        'profession' => 'nullable|string',
                    ]);

                    ParentModel::create([
                        'user_id' => $utilisateur->id,
                        'nom' => $utilisateur->nom,
                        'prenom' => $parentData['prenom'],
                        'email' => $utilisateur->email,
                        'telephone' => $utilisateur->telephone,
                        'adresse' => $utilisateur->adresse,
                        'profession' => $parentData['profession'] ?? null,
                    ]);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $utilisateur,
                'message' => 'Utilisateur créé avec succès'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Erreur de validation'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
    public function update(Request $request, $id)
{
    $utilisateur = Utilisateur::findOrFail($id);

    $validatedData = $request->validate([
        'nom' => 'nullable|string',
        'email' => 'nullable|email|unique:utilisateurs,email,' . $id,
        'telephone' => 'nullable|string',
        'adresse' => 'nullable|string',
        'role' => 'in:admin,professeur,surveillant,étudiant,parent',
    ]);

    if ($request->hasFile('photo_profil')) {
        $chemin = $request->file('photo_profil')->store('photos', 'public');
        $utilisateur->photo_profil = $chemin;
    }

    $utilisateur->update([
        'nom' => $validatedData['nom'],
        'email' => $validatedData['email'],
        'telephone' => $validatedData['telephone'] ?? null,
        'adresse' => $validatedData['adresse'] ?? null,
        'role' => $validatedData['role'],
    ]);

    // Mise à jour spécifique selon le rôle
    switch ($utilisateur->role) {
        case 'admin':
            $utilisateur->admin()->update([
                'name' => $validatedData['nom'],
                'email' => $validatedData['email'],
            ]);
            break;

        case 'professeur':
            $profData = $request->validate([
                'niveau_enseignement' => 'nullable|string',
                'diplome' => 'nullable|string',
                'date_embauche' => 'nullable|date',
            ]);

            $utilisateur->professeur()->update([
                'nom' => $validatedData['nom'],
                'email' => $validatedData['email'],
                'niveau_enseignement' => $profData['niveau_enseignement'],
                'diplome' => $profData['diplome'],
                'date_embauche' => $profData['date_embauche'],
            ]);
            break;

        case 'surveillant':
            $utilisateur->surveillant()->update([
                'nom' => $validatedData['nom'],
                'email' => $validatedData['email'],
            ]);
            break;

        case 'étudiant':
            $etudiantData = $request->validate([
                'prenom' => 'required|string',
                'date_naissance' => 'required|date',
                'sexe' => 'required|in:M,F',
                'montant_a_payer' => 'nullable|numeric',
                'classe_id' => 'nullable|exists:classrooms,id',
                'origine' => 'nullable|string',
                'parent_id' => 'nullable|exists:parents,id',
            ]);

            $utilisateur->etudiant()->update([
                'nom' => $validatedData['nom'],
                'prenom' => $etudiantData['prenom'],
                'date_naissance' => $etudiantData['date_naissance'],
                'sexe' => $etudiantData['sexe'],
                'montant_a_payer' => $etudiantData['montant_a_payer'] ?? 0,
                'classe_id' => $etudiantData['classe_id'] ?? null,
                'origine' => $etudiantData['origine'] ?? null,
                'parent_id' => $etudiantData['parent_id'] ?? null,
                'adresse' => $validatedData['adresse'] ?? null,
                'photo_profil' => $utilisateur->photo_profil,
            ]);
            break;

        case 'parent':
            $parentData = $request->validate([
                'prenom' => 'required|string',
                'profession' => 'nullable|string',
            ]);

            $utilisateur->parent()->update([
                'nom' => $validatedData['nom'],
                'prenom' => $parentData['prenom'],
                'email' => $validatedData['email'],
                'telephone' => $validatedData['telephone'],
                'adresse' => $validatedData['adresse'],
                'profession' => $parentData['profession'] ?? null,
            ]);
            break;
    }

    return response()->json([
        'success' => true,
        'message' => 'Utilisateur mis à jour avec succès',
        'data' => $utilisateur
    ]);
}

     public function affecterProfesseurs(Request $request, $etudiantId)
     {
         $etudiant = Etudiant::findOrFail($etudiantId);
 
         $request->validate([
             'professeurs' => 'required|array',
             'professeurs.*' => 'exists:professeurs,id',
         ]);
 
         $etudiant->professeurs()->sync($request->professeurs);
 
         return response()->json(['message' => 'Professeurs affectés avec succès!']);
     }
 
    public function show($id)
     {
         $utilisateur = Utilisateur::with([
             'etudiant',
             'professeur',
             'parent',
             'admin',
             'surveillant'
         ])->findOrFail($id);
     
         return response()->json($utilisateur);
     }

   
    public function destroy($id)
    {
        Utilisateur::destroy($id);
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    public function associerEtudiantProfesseur(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'professeur_id' => 'required|exists:professeurs,id'
        ]);
    
        try {
            // Vérifier si l'association existe déjà
            $exists = DB::table('etudiant_professeur')
                ->where('etudiant_id', $request->etudiant_id)
                ->where('professeur_id', $request->professeur_id)
                ->exists();
    
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette association existe déjà'
                ], 409);
            }
    
            // Créer l'association
            DB::table('etudiant_professeur')->insert([
                'etudiant_id' => $request->etudiant_id,
                'professeur_id' => $request->professeur_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Association créée avec succès'
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }  
}
