<?php
namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UtilisateurController extends Controller
{
    public function index()
    {
        return response()->json(Utilisateur::all());
    }

    public function store(Request $request)
    {
        // Validation des champs
        $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs',
            'mot_de_passe' => 'required|min:6',
            'role' => 'required|in:admin,professeur,surveillant,étudiant,parent',
            'matricule' => 'required|unique:utilisateurs', // Validation du matricule
        ]);

        // Création de l'utilisateur
        $utilisateur = Utilisateur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'mot_de_passe' => bcrypt($request->mot_de_passe),
            'role' => $request->role,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo_profil' => $request->photo_profil,
            'matricule' => $request->matricule,  // Enregistrement du matricule
        ]);

        return response()->json($utilisateur, 201);
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
