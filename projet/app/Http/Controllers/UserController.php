<?php
namespace App\Http\Controllers;

use App\Models\User; // Assurez-vous que c'est le bon modèle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all()); // Retourner tous les utilisateurs
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email|unique:user', // Utilisation de la table user
            'mot_de_passe' => 'required|min:6',
            'role' => 'required|in:admin,professeur,surveillant,étudiant,parent'
        ]);

        $utilisateur = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'mot_de_passe' => bcrypt($request->mot_de_passe),
            'role' => $request->role,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo_profil' => $request->photo_profil,
        ]);

        return response()->json($utilisateur, 201);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id)); // Afficher un utilisateur spécifique
    }

    public function update(Request $request, $id)
    {
        $utilisateur = User::findOrFail($id);
        $utilisateur->update($request->only('nom', 'email', 'role'));

        return response()->json($utilisateur);
    }

    public function destroy($id)
    {
        User::destroy($id); // Suppression de l'utilisateur
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}
