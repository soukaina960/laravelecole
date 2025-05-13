<?php
namespace App\Http\Controllers;

use App\Models\FichierPedagogique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FichierPedagogiqueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = FichierPedagogique::with(['professeur', 'classe', 'semestre', 'matiere'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('classe_id')) {
                $query->where('classe_id', $request->classe_id);
            }
            if ($request->has('semestre_id')) {
                $query->where('semestre_id', $request->semestre_id);
            }
            if ($request->has('matiere_id')) {
                $query->where('matiere_id', $request->matiere_id);
            }
            if ($request->has('professeur_id')) {
                $query->where('professeur_id', $request->professeur_id);
            }

            $fichiers = $query->get();

            return response()->json([
                'success' => true,
                'data' => $fichiers
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur FichierPedagogiqueController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des fichiers'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'professeur_id' => 'required|exists:professeurs,id',
                'matiere_id' => 'required|exists:matieres,id',
                'classe_id' => 'required|exists:classrooms,id', // Changé de classrooms à classes
                'semestre_id' => 'required|exists:semestres,id',
                'type_fichier' => 'required|in:cours,devoir,examen,corrigé,ressource', // Ajout des types manquants
                'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx', // Types MIME correspondants
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$request->hasFile('fichier')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier reçu.'
                ], 400);
            }

            $file = $request->file('fichier');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('fichiers_pedagogiques', $fileName, 'public');

            $fichier = FichierPedagogique::create([
                'professeur_id' => $request->professeur_id,
                'matiere_id' => $request->matiere_id,
                'classe_id' => $request->classe_id,
                'semestre_id' => $request->semestre_id,
                'type_fichier' => $request->type_fichier,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $filePath,
                'taille_fichier' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fichier enregistré avec succès',
                'data' => $fichier
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur FichierPedagogiqueController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fichier = FichierPedagogique::findOrFail($id);

            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($fichier->chemin_fichier)) {
                Storage::disk('public')->delete($fichier->chemin_fichier);
            }

            $fichier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur FichierPedagogiqueController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $fichier = FichierPedagogique::findOrFail($id);
            
            if (!Storage::disk('public')->exists($fichier->chemin_fichier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur'
                ], 404);
            }

            return Storage::disk('public')->download($fichier->chemin_fichier, $fichier->nom_fichier);

        } catch (\Exception $e) {
            Log::error('Erreur FichierPedagogiqueController@download: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement'
            ], 500);
        }
    }

    public function fichiersPourEtudiant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'classe_id' => 'required|exists:classrooms,id',
                'semestre_id' => 'required|exists:semestres,id',
                'matiere_id' => 'nullable|exists:matieres,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = FichierPedagogique::with(['professeur', 'matiere'])
                ->where('classe_id', $request->classe_id)
                ->where('semestre_id', $request->semestre_id);

            if ($request->has('matiere_id')) {
                $query->where('matiere_id', $request->matiere_id);
            }

            $fichiers = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $fichiers
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur FichierPedagogiqueController@fichiersPourEtudiant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des fichiers'
            ], 500);
        }
    }
}