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
        // Authentification réussie :
        $utilisateur = Utilisateur::where('matricule', $request->matricule)->first();

        if ($utilisateur->role === 'parent') {
            $utilisateur->load('parent'); // ← charge la relation parent
        }


        if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
            return response()->json(['message' => 'Matricule ou mot de passe incorrect'], 401);
        }

        // Création du token d'authentification
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        // Recherche du parent lié à cet utilisateur
        $parent = $utilisateur->parent; 
        $surveillant = $utilisateur->surveillant; // ← charge la relation surveillant
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'utilisateur' => $utilisateur,
            'role' => $utilisateur->role,
            'parent' => $parent,
            'surveillant' => $surveillant, // ← renvoie le surveillant
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur login : ' . $e->getMessage());
        return response()->json(['message' => 'Erreur serveur: ' . $e->getMessage()], 500);
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
}






