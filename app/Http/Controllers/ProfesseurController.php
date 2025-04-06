<?php

namespace App\Http\Controllers;

use App\Models\Professeur;
use Illuminate\Http\Request;

class ProfesseurController extends Controller
{
    // Récupérer tous les professeurs avec l'utilisateur lié
    public function index()
    {
        $professeurs = Professeur::with('utilisateur')->get();
        return response()->json($professeurs);
    }

    // Créer un nouveau professeur
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:utilisateurs,id',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:utilisateurs,email',
            'specialite' => 'required|string|max:255',
            'niveau_enseignement' => 'required|string|max:255',
            'diplome' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        $professeur = Professeur::create([
            'user_id' => $request->user_id,
            'nom'=> $request->nom,
            'email'=> $request->email,
            'specialite' => $request->specialite,
            'niveau_enseignement' => $request->niveau_enseignement,
            'diplome' => $request->diplome,
            'date_embauche' => $request->date_embauche,
        ]);

        return response()->json($professeur, 201);
    }

    // Modifier un professeur
    public function update(Request $request, $id)
    {
        $professeur = Professeur::findOrFail($id);

        $request->validate([
            'specialite' => 'sometimes|required|string|max:255',
            'niveau_enseignement' => 'sometimes|required|string|max:255',
            'diplome' => 'sometimes|required|string|max:255',
            'date_embauche' => 'sometimes|required|date',
        ]);

        $professeur->update($request->all());

        return response()->json($professeur, 200);
    }
    public function calculerSalaire(Request $request, $id)
    {
        $professeur = Professeur::findOrFail($id);
    
        // Validation des champs reçus
        $request->validate([
            'prime' => 'required|numeric',
            'pourcentage' => 'required|numeric',
        ]);
    
        // Mise à jour des valeurs prime et pourcentage dans la base de données
        $professeur->prime = $request->prime;
        $professeur->pourcentage = $request->pourcentage;
        $professeur->save();  // Sauvegarde dans la BD
    
        // Calcul du salaire après mise à jour des données
        $totalMontants = $professeur->etudiants->sum(function ($etudiant) {
            return $etudiant->montant_a_payer;
        });
    
        $salaire = ($professeur->pourcentage / 100) * $totalMontants + $professeur->prime;
        $professeur->total = $salaire;
        $professeur->save(); 
    
        return response()->json(['salaire' => $salaire], 200);
    }
    
    
    // Supprimer un professeur
    public function destroy($id)
    {
        $professeur = Professeur::findOrFail($id);
        $professeur->delete();

        return response()->json(['message' => 'Professeur supprimé avec succès'], 200);
    }
}
