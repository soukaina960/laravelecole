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
    
            // Charge les relations possibles en une seule requête
            $utilisateur = Utilisateur::where('matricule', $request->matricule)
                ->with(['etudiant', 'parent', 'admin', 'professeur']) // Ajout de la relation professeur
                ->first();
    
            if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
                return response()->json([
                    'message' => 'Matricule ou mot de passe incorrect'
                ], 401);
            }
    
            // Données de base de l'utilisateur
            $userData = [
                'id' => $utilisateur->id,
                'matricule' => $utilisateur->matricule,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'role' => $utilisateur->role,
            ];
    
            // Ajout des données spécifiques au rôle
            switch ($utilisateur->role) {
                case 'étudiant':
                    if ($utilisateur->etudiant) {
                        $userData['classe_id'] = $utilisateur->etudiant->classe_id;
                    }
                    break;
                    
                case 'professeur':
                    if ($utilisateur->professeur) {
                        $userData['professeur_id'] = $utilisateur->professeur->id;
                    }
                    break;
                    
                case 'parent':
                    if ($utilisateur->parent) {
                        $userData['parent_id'] = $utilisateur->parent->id;
                    }
                    break;
                    
                case 'admin':
                    if ($utilisateur->admin) {
                        $userData['admin_id'] = $utilisateur->admin->id;
                    }
                    break;
            }
    
            // Création du token
            $token = $utilisateur->createToken('auth_token')->plainTextToken;
    
            // Réponse de base
            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'utilisateur' => $userData,
                'role' => $utilisateur->role,
            ];
    
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
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
                'matricule' => $validated['matricule'],
                'nom' => $validated['nom'],
                'email' => $validated['email'],
                'mot_de_passe' => Hash::make($validated['mot_de_passe']),
                'role' => $validated['role'],
                'telephone' => $validated['telephone'] ?? null,
                'adresse' => $validated['adresse'] ?? null,
            ]);

            return response()->json([
                'utilisateur' => $utilisateur,
                'message' => 'Compte créé avec succès',
            ]);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la création du compte',
                'errors' => $e->errors() ?? null
            ], 500);
        }
    }
}