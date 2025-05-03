<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ParentModel;
use App\Models\PaiementMensuel;
use Barryvdh\DomPDF\Facade as PDF;

class ParentController extends Controller
{
    /**
     * Affiche l'email du parent d'un étudiant spécifique
     * 
     * @param int $etudiant_id L'ID de l'étudiant
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $parents = ParentModel::select(['id', 'nom', 'prenom', 'email', 'telephone', 'adresse', 'profession'])
                       ->orderBy('nom')
                       ->get();
        
        return response()->json([
            'success' => true,
            'parents' => $parents
        ]);
    }
    public function update(Request $request, $id)
{
    try {
        // Trouver le parent par ID
        $parent = ParentModel::findOrFail($id);

        // Trouver l'utilisateur lié au parent
        $user = Utilisateur::findOrFail($parent->user_id);

        // Validation des données
        $validatedData = $request->validate([
            'parent.nom' => 'required|string|max:255',
            'parent.prenom' => 'required|string|max:255',
            'parent.telephone' => 'nullable|string|max:20',
            'user.email' => 'required|email|max:255',
            'user.password' => 'nullable|string|min:6',
        ]);

        // Mise à jour des informations du parent
        $parent->update([
            'nom' => $validatedData['parent']['nom'],
            'prenom' => $validatedData['parent']['prenom'],
            'telephone' => $validatedData['parent']['telephone'] ?? $parent->telephone,
        ]);

        // Mise à jour des informations de l'utilisateur
        $user->email = $validatedData['user']['email'];

        if (!empty($validatedData['user']['password'])) {
            // Si un nouveau mot de passe est fourni, le hacher
            $user->password = bcrypt($validatedData['user']['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'parent' => $parent,
            'user' => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la mise à jour du profil.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function getParentEmail($etudiant_id)
    {
    
        try {
            // 1. Récupérer l'étudiant avec son parent
            $etudiant = Etudiant::with(['parent' => function($query) {
                $query->select('id', 'email'); // On ne sélectionne que l'email du parent
            }])
            ->select('id', 'parent_id') // On ne sélectionne que les champs nécessaires
            ->findOrFail($etudiant_id);

            // 2. Vérifier si un parent est associé
            if (!$etudiant->parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun parent associé à cet étudiant'
                ], 404);
            }

            // 3. Retourner la réponse
            return response()->json([
                'success' => true,
                'etudiant_id' => $etudiant->id,
                'parent_email' => $etudiant->parent->email,
                'parent_info' => [
                    'nom' => $etudiant->parent->nom,
                    'prenom' => $etudiant->parent->prenom
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur dans ParentEmailController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }
    public function show($id)
    {
        $parent = ParentModel::find($id);
        if (!$parent) {
            return response()->json(['message' => 'Parent non trouvé'], 404);
        }
        return response()->json($parent);
    }
    public function getByUserId($user_id)
{
    $parent = ParentModel::where('user_id', $user_id)->first();

    if (!$parent) {
        return response()->json(['message' => 'Parent introuvable'], 404);
    }

    return response()->json($parent);
}
public function paiementsDuParent($parentId)
{
    // جميع الأولاد ديال هاد الوالد
    $enfants = Etudiant::where('parent_id', $parentId)->pluck('id');

    // جميع الـ paiements ديال هاد الأولاد
    $paiements = PaiementMensuel::whereIn('etudiant_id', $enfants)->get();

    return response()->json($paiements);
}





}