<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'matricule' => 'required|string',
                'mot_de_passe' => 'required|string',
            ]);

            // Recherche de l'utilisateur par le matricule
            $utilisateur = Utilisateur::where('matricule', $request->matricule)->first();

            if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
                return response()->json(['message' => 'Matricule ou mot de passe incorrect'], 401);
            }

            // Vérifier si le mot de passe a déjà été changé
            $requiresPasswordChange = !$utilisateur->password_changed;

            // Charger les relations selon le rôle
            if ($utilisateur->role === 'parent') {
                $utilisateur->load('parent');
            } elseif ($utilisateur->role === 'surveillant') {
                $utilisateur->load('surveillant');
            } elseif ($utilisateur->role === 'étudiant') {
                $utilisateur->load('etudiant');
            } elseif ($utilisateur->role === 'professeur') {
                $utilisateur->load('professeur');
            }

            // Création du token d'authentification
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            // Préparer les données selon le rôle
            $parent = $utilisateur->parent ?? null;
            $surveillant = $utilisateur->surveillant ?? null;
            $etudiant = $utilisateur->etudiant ?? null;
            $professeur = $utilisateur->professeur ?? null;

            // Si c'est un étudiant, récupérer classe_id
            $classe_id = $etudiant ? $etudiant->classe_id : null;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'utilisateur' => $utilisateur,
                'role' => $utilisateur->role,
                'parent' => $parent,
                'surveillant' => $surveillant,
                'professeur' => $professeur,
                'etudiant' => $etudiant,
                'classe_id' => $classe_id,
                'requires_password_change' => $requiresPasswordChange,
                'message' => $requiresPasswordChange ? 'Veuillez changer votre mot de passe' : 'Connexion réussie'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur login : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

   public function changePassword(Request $request)
{
    try {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Correction ici
        $utilisateur = $request->user();

        if (!Hash::check($request->current_password, $utilisateur->mot_de_passe)) {
            return response()->json([
                'message' => 'Mot de passe actuel incorrect',
                'errors' => ['current_password' => ['Le mot de passe actuel est incorrect.']]
            ], 422);
        }

        $utilisateur->mot_de_passe = Hash::make($request->new_password);
        $utilisateur->password_changed = true;
        $utilisateur->save();

        return response()->json([
            'message' => 'Mot de passe changé avec succès',
            'password_changed' => true
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur changement mot de passe: ' . $e->getMessage());
        return response()->json([
            'message' => 'Erreur lors du changement de mot de passe'
        ], 500);
    }
}


    public function register(Request $request)
    {
        try {
            $request->validate([
                'matricule' => 'required|string|unique:utilisateurs',
                'nom' => 'required|string',
                'email' => 'required|email|unique:utilisateurs',
                'mot_de_passe' => 'required|string|min:8',
                'mot_de_passe_confirmation' => 'required|string|same:mot_de_passe',
                'role' => 'required|string|in:admin,professeur,surveillant,étudiant,parent',
                'telephone' => 'nullable|string',
                'adresse' => 'nullable|string',
            ]);

            $utilisateur = Utilisateur::create([
                'matricule' => $request->matricule,
                'nom' => $request->nom,
                'email' => $request->email,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'role' => $request->role,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'password_changed' => true, // Pour les inscriptions manuelles, le mot de passe est déjà changé
            ]);

            return response()->json([
                'utilisateur' => $utilisateur,
                'message' => 'Compte créé avec succès',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur de création du compte: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'message' => 'Déconnexion réussie'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur déconnexion: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la déconnexion'], 500);
        }
    }
}