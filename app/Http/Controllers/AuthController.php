<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'matricule' => 'required|string',
                'mot_de_passe' => 'required|string'
            ]);

            $utilisateur = Utilisateur::where('matricule', $request->matricule)->first();

            if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
                return response()->json(['message' => 'Matricule ou mot de passe incorrect'], 401);
            }

            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'utilisateur' => $utilisateur->only([
                    'id', 'matricule', 'nom', 'email', 
                    'role', 'telephone', 'adresse', 'photo_profil'
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur de connexion: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la connexion'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Déconnexion réussie']);

        } catch (\Exception $e) {
            Log::error('Erreur de déconnexion: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la déconnexion'], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'matricule' => 'required|string|unique:utilisateurs|max:20',
                'nom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs|max:255',
                'mot_de_passe' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,enseignant,etudiant',
                'telephone' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:255'
            ]);

            $utilisateur = Utilisateur::create([
                'matricule' => $request->matricule,
                'nom' => $request->nom,
                'email' => $request->email,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'role' => $request->role,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse
            ]);

            return response()->json([
                'message' => 'Compte créé avec succès',
                'utilisateur' => $utilisateur
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erreur de création de compte: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création du compte'], 500);
        }
    }
}