<?php
namespace App\Http\Controllers;

use App\Models\FichierPedagogique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
            // Validation des données
            $validator = Validator::make($request->all(), [
                'professeur_id' => 'required|exists:professeurs,id',
                'matiere_id' => 'required|exists:matieres,id',
                'classe_id' => 'required|exists:classrooms,id',
                'semestre_id' => 'required|exists:semestres,id',
                'type_fichier' => 'required|in:cours,devoir,examen',
                'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar', // Vérification du fichier ici
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Vérifie si le fichier est bien reçu
            if ($request->hasFile('fichier')) {
                $file = $request->file('fichier');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('fichiers', $fileName, 'public'); // Sauvegarde le fichier dans le dossier 'fichiers'
    
                // Crée l'entrée dans la base de données
                $fichier = FichierPedagogique::create([
                    'professeur_id' => $request->professeur_id,
                    'matiere_id' => $request->matiere_id,
                    'classe_id' => $request->classe_id,
                    'semestre_id' => $request->semestre_id,
                    'type_fichier' => $request->type_fichier,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin_fichier' => $filePath,  // Le chemin du fichier qui a été stocké
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Fichier enregistré avec succès',
                    'data' => $fichier
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier reçu.'
                ], 400);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
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
        $fichier = FichierPedagogique::find($id);
    
        if (!$fichier) {
            return response()->json(['success' => false, 'message' => 'Fichier introuvable dans la base de données'], 404);
        }
    
        $chemin = storage_path('app/public/fichiers/' . $fichier->nom_fichier); // ou $fichier->chemin si tu as un champ chemin
    
        if (!file_exists($chemin)) {
            return response()->json(['success' => false, 'message' => 'Fichier introuvable sur le serveur'], 404);
        }
    
        return response()->download($chemin);
    }
    
    public function fichiersPourEtudiant(Request $request)
    {
        // Vérification des paramètres
        $classeId = $request->input('classe_id');
        $semestreId = $request->input('semestre_id');
        $matiereId = $request->input('matiere_id'); // Paramètre pour la matière
        
        Log::info("Paramètres reçus :", [
            'classe_id' => $classeId,
            'semestre_id' => $semestreId,
            'matiere_id' => $matiereId
        ]);
    
        if (!$classeId || !$semestreId) {
            return response()->json(['message' => 'Classe ou semestre manquant'], 400);
        }
    
        // Construction de la requête
        $query = FichierPedagogique::where('classe_id', $classeId)
                                    ->where('semestre_id', $semestreId)
                                    ->join('professeurs', 'fichiers.professeur_id', '=', 'professeurs.id')
                                    ->select('fichiers.*', 'professeurs.specialite');  // Sélectionner aussi la spécialité
    
        // Ajouter un filtre pour la matière si spécifié
        if ($matiereId) {
            $query->where('fichiers.matiere_id', $matiereId);  // Filtrer par matière
        }
    
        // Récupérer les fichiers
        $fichiers = $query->get();
    
        return response()->json($fichiers);
    }
    

}