<?php

namespace App\Http\Controllers;

use App\Models\Surveillant;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SurveillantController extends Controller
{
    // 📄 Afficher tous les surveillants
    public function index()
    {
        // Utilise le 'role' pour filtrer les surveillants
        $surveillants = Surveillant::where('role', 'surveillant')->get();
        return response()->json($surveillants);
    }
    

    //  Afficher le formulaire de création
    public function create()
    {
        return view('surveillants.create');
    }

    // ✅ Enregistrer un nouveau surveillant
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs',
            'matricule' => 'required|string|unique:utilisateurs',
            'mot_de_passe' => 'required|string|min:6',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            'photo_profil' => 'nullable|string',
        ]);

        $data['role'] = 'surveillant';
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);

        Surveillant::create($data);

        return redirect()->route('surveillants.index')->with('success', 'Surveillant ajouté avec succès !');
    }

    // 👁️ Afficher un seul surveillant + ses relations
    public function show($id)
    {
        $surveillant = Surveillant::with('utilisateur')->find($id);
        if ($surveillant) {
            return response()->json($surveillant);
        }
        return response()->json(['message' => 'Surveillant non trouvé'], 404);
    }
    // ✏️ Afficher le formulaire de modification
    // public function edit($id)
    // {
    //     $surveillant = Surveillant::findOrFail($id);
    //     return view('surveillants.edit', compact('surveillant'));
    // }

    // 🔄 Mettre à jour les infos du surveillant
    public function update(Request $request, $id)
    {
        try {
            // Trouver le parent par ID
            $surveillant = Surveillant::findOrFail($id);
    
            // Trouver l'utilisateur lié au surveillant
            // Assurez-vous que la relation est définie dans le modèle Surveillant
            $utilisateur = $surveillant->utilisateur;
            
            $utilisateur = Utilisateur::findOrFail($surveillant->user_id);
    
            // Validation des données
            $validatedData = $request->validate([
                'surveillant.nom' => 'required|string|max:255',
                'surveillant.prenom' => 'required|string|max:255',
                'surveillant.telephone' => 'nullable|string|max:20',
                'surveillant.email' => 'required|email|max:255',
                'surveillant.password' => 'nullable|string|min:6',
            ]);
    
            // Mise à jour des informations du parent
            $surveillant->update([
                'nom' => $validatedData['surveillant']['nom'],
                'prenom' => $validatedData['surveillant']['prenom'],
                'telephone' => $validatedData['surveillant']['telephone'] ?? $surveillant->telephone,
            ]);
    
            // Mise à jour des informations de l'utilisateur
            $utilisateur->update([
                'nom' => $validatedData['surveillant']['nom'],
                'prenom' => $validatedData['surveillant']['prenom'],
                'telephone' => $validatedData['surveillant']['telephone'] ?? $surveillant->telephone,
                'email' => $validatedData['surveillant']['email'],
                'password' => $validatedData['surveillant']['password'] 
                    ? bcrypt($validatedData['surveillant']['password']) 
                    : $utilisateur->password
            ]);
            
    
            if (!empty($validatedData['utilisateur']['password'])) {
                // Si un nouveau mot de passe est fourni, le hacher
                $utilisateur->password = bcrypt($validatedData['utilisateur']['password']);
            }
    
            $utilisateur->save();
    
            return response()->json([
                'message' => 'Profil mis à jour avec succès.',
                'surveillant' => $surveillant,
                'utilisateur' => $utilisateur
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour du profil.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // 🗑️ Supprimer un surveillant
    public function destroy($id)
    {
        $surveillant = Surveillant::findOrFail($id);
        $surveillant->delete();

        return redirect()->route('surveillants.index')->with('success', 'Surveillant supprimé avec succès !');
    }
}