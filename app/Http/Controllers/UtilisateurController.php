<?php
namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\ParentModel;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            // Validation des données de base
            $validatedData = $request->validate([
                'telephone' => 'nullable|string',
                'nom' => 'required|string',
                'email' => 'required|email|unique:utilisateurs',
                'mot_de_passe' => 'required|min:6',
                'role' => 'required|in:admin,professeur,surveillant,étudiant,parent',
                'matricule' => 'required|unique:utilisateurs',
            ]);

            // Gestion de l'image de profil (s'il y en a une)
            $chemin = 'default_image.png'; // Valeur par défaut
            if ($request->hasFile('photo_profil')) {
                $chemin = $request->file('photo_profil')->store('photos', 'public');
            }

            // Création de l'utilisateur
            $utilisateur = Utilisateur::create([
                'nom' => $validatedData['nom'],
                'email' => $validatedData['email'],
                'mot_de_passe' => bcrypt($validatedData['mot_de_passe']),
                'role' => $validatedData['role'],
                'telephone' => $validatedData['telephone'] ?? null,
                'adresse' => $request->adresse ?? null,
                'photo_profil' => $chemin,
                'matricule' => $validatedData['matricule'],
            ]);

            // Création spécifique pour un étudiant
            if ($utilisateur->role === 'étudiant') {
                $studentData = $request->validate([
                    'prenom' => 'required|string',
                    'date_naissance' => 'required|date',
                    'sexe' => 'required|in:M,F',
                    'montant_a_payer' => 'nullable|numeric',
                    'classe_id' => 'nullable|exists:classrooms,id',
                ]);

                Etudiant::create([
                    'utilisateur_id' => $utilisateur->id,
                    'nom' => $validatedData['nom'],
                    'prenom' => $studentData['prenom'],
                    'matricule' => $validatedData['matricule'],
                    'email' => $validatedData['email'],
                    'origine' => $request->origine ?? null,
                    'parent_id' => $request->parent_id ?? null,
                    'date_naissance' => $studentData['date_naissance'],
                    'sexe' => $studentData['sexe'],
                    'adresse' => $request->adresse ?? null,
                    'photo_profil' => $chemin,
                    'montant_a_payer' => $request->montant_a_payer ?? 0,
                    'classe_id' => $request->classe_id ?? null,
                ]);
            }

            if ($utilisateur->role === 'professeur') {
                $profData = $request->validate([
                    'specialite' => 'nullable|string',
                    'niveau_enseignement' => 'nullable|string',
                    'diplome' => 'nullable|string',
                    'date_embauche' => 'nullable|date',
                ]);
                $professeur = Professeur::create([
                    'user_id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'email' => $utilisateur->email,
                    'specialite' => $profData['specialite'],
                    'niveau_enseignement' => $profData['niveau_enseignement'],
                    'diplome' => $profData['diplome'],
                    'date_embauche' => $profData['date_embauche'],
                ]);

               
             }
             if ($utilisateur->role === 'parent') {
                $parentData = $request->validate([
                    'prenom' => 'required|string',
                    'profession' => 'nullable|string',
                ]);
                
                // Assurez-vous que votre modèle s'appelle correctement (par exemple ParentModel)
                ParentModel::create([
                    'utilisateur_id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'prenom' => $parentData['prenom'],
                    'email' => $utilisateur->email,
                    'telephone' => $utilisateur->telephone,
                    'adresse' => $utilisateur->adresse,
                    'profession' => $parentData['profession'] ?? null,
                ]);
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
 
            
 

    public function show($id)
    {
        return response()->json(Utilisateur::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        // Mise à jour des champs, y compris le matricule
        $utilisateur->update($request->only('nom', 'email', 'role', 'matricule')); // Ajout du matricule

        return response()->json($utilisateur);
    }

    public function destroy($id)
    {
        Utilisateur::destroy($id);
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    // Relation avec l'étudiant
    public function etudiant()
    {
        return $this->hasOne(Etudiant::class, 'utilisateur_id');
    }
}
