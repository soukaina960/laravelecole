<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255', // Le nom est requis
            'email' => 'required|email|unique:etudiants,email', // Vérifie que l'email est unique
            'matricule' => 'required|string|unique:etudiants,matricule',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string',
            'photo_profil' => 'nullable|image',
            'class_id' => 'required|exists:classrooms,id',
            'montant_a_payer' => 'nullable|numeric',
            'professeurs' => 'nullable|array',
        ]);


        $etudiant = Etudiant::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'matricule' => $request->matricule,
            'date_naissance' => $request->date_naissance,
            'sexe' => $request->sexe,
            'adresse' => $request->adresse,
            'photo_profil' => $request->photo_profil,
            'class_id' => $request->class_id, 
           'montant_a_payer' => $request->montant_a_payer,
           
        ]);
        
        if ($request->has('professeurs')) {
            $etudiant->professeurs()->attach($request->professeurs);
        }
    
        return response()->json($etudiant, 201);
    }

   
    public function index()
    {
        $etudiants = Etudiant::with('classroom')->get();
    
        // Exclure les données binaires ou encoder en Base64
        $etudiants->each(function ($etudiant) {
            $etudiant->photo_profil = base64_encode($etudiant->photo_profil); // Encodage Base64
        });
    
        return response()->json($etudiants);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'matricule' => 'required|string',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'montant_a_payer' => 'nullable|numeric', 
            'professeurs' => 'nullable|array',
        ]);

        $etudiant = Etudiant::findOrFail($id);
        $etudiant->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'matricule' => $request->matricule,
            'date_naissance' => $request->date_naissance,
            'sexe' => $request->sexe,
            'adresse' => $request->adresse,
            'class_id' => $request->class_id,
            'montant_a_payer' => $request->montant_a_payer, // Mise à jour du montant
        ]);

        return response()->json($etudiant);
    }
    public function destroy($id)
    {
        $etudiant = Etudiant::findOrFail($id);
            $etudiant->delete();
    
        return response()->json(['message' => 'Étudiant supprimé avec succès!']);
    }
    
public function affecterProfesseurs(Request $request, $etudiantId)
{
    $etudiant = Etudiant::findOrFail($etudiantId);

    $request->validate([
        'professeurs' => 'required|array', // Professeurs à affecter
        'professeurs.*' => 'exists:professeurs,id', // Les IDs des professeurs doivent exister
    ]);

    // Associer les professeurs à l'étudiant
    $etudiant->professeurs()->sync($request->professeurs);

    return response()->json(['message' => 'Professeurs affectés avec succès!']);
}

}
