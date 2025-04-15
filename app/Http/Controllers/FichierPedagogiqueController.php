<?php

namespace App\Http\Controllers;

use App\Models\FichierPedagogique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Fichier;

class FichierPedagogiqueController extends Controller
{
    /**
     * Récupère la liste des fichiers avec filtres
     */
    public function index(Request $request)
    {
        try {
            $query = FichierPedagogique::with(['professeur', 'classe', 'semestre'])
                ->orderBy('created_at', 'desc');

            if ($request->has('professeur_id')) {
                $query->where('professeur_id', $request->professeur_id);
            }

            $fichiers = $query->get();

            return response()->json([
                'success' => true,
                'data' => $fichiers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des fichiers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre un nouveau fichier
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'professeur_id' => 'required|exists:professeurs,id',
                'classe_id' => 'required|exists:classes,id',
                'semestre_id' => 'required|exists:semestres,id',
                'type' => 'required|in:cours,devoir,examen',
                'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('fichier');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('fichiers', $fileName, 'public');

            $fichier = FichierPedagogique::create([
                'professeur_id' => $request->professeur_id,
                'classe_id' => $request->classe_id,
                'semestre_id' => $request->semestre_id,
                'type_fichier' => $request->type,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $filePath,
                'taille_fichier' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fichier enregistré avec succès',
                'data' => $fichier
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche un fichier spécifique
     */
    public function show($id)
    {
        try {
            $fichier = FichierPedagogique::with(['professeur', 'classe', 'semestre'])->find($id);

            if (!$fichier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $fichier
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du fichier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour un fichier existant
     */
    public function update(Request $request, $id)
    {
        try {
            $fichier = FichierPedagogique::find($id);

            if (!$fichier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|in:cours,devoir,examen',
                'fichier' => 'sometimes|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('fichier')) {
                Storage::disk('public')->delete($fichier->chemin_fichier);
                $file = $request->file('fichier');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('fichiers', $fileName, 'public');

                $fichier->update([
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin_fichier' => $filePath,
                    'taille_fichier' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension()
                ]);
            }

            if ($request->has('type')) {
                $fichier->update([
                    'type_fichier' => $request->type
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Fichier mis à jour avec succès',
                'data' => $fichier
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un fichier
     */
    public function destroy($id)
    {
        try {
            $fichier = FichierPedagogique::find($id);

            if (!$fichier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            Storage::disk('public')->delete($fichier->chemin_fichier);
            $fichier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharge un fichier
     */
    public function download($id)
    {
        try {
            $fichier = FichierPedagogique::find($id);

            if (!$fichier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $fichier->chemin_fichier);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur'
                ], 404);
            }

            return response()->download($filePath, $fichier->nom_fichier);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fichiersPourEtudiant(Request $request)
    {
        // Vérification des paramètres
        $classeId = $request->input('classe_id');
        $semestreId = $request->input('semestre_id');
    
        if (!$classeId || !$semestreId) {
            return response()->json(['message' => 'Classe ou semestre manquant'], 400);
        }
    
        // Récupérer les fichiers avec la spécialité du professeur
        $fichiers = Fichier::where('classe_id', $classeId)
                            ->where('semestre_id', $semestreId)
                            ->join('professeurs', 'fichiers.professeur_id', '=', 'professeurs.id')
                            ->select('fichiers.*', 'professeurs.specialite')  // Sélectionner aussi la spécialité
                            ->get();
    
        return response()->json($fichiers);
    }
}