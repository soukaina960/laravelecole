<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examen;
use App\Models\Professeur;
use App\Models\Classroom;
use App\Models\Matiere;
use Illuminate\Support\Facades\Auth; // Correct import for Auth facade
 use App\Models\Etudiant;


class ExamenController extends Controller
{
    // Ajouter un examen
    public function store(Request $request)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'classe_id' => 'required|exists:classrooms,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'nullable|exists:professeurs,id',
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i:s',
            'heure_fin' => 'required|date_format:H:i:s|after:heure_debut',
            'jour' => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche',
        ]);

        // Si l'utilisateur est un professeur, on lui assigne automatiquement son ID comme professeur_id
        if (Auth::check() && Auth::user()->role === 'professeur') { // Changed from utilisateur() to user()
            $validated['professeur_id'] = Auth::id();
        }

        // Création de l'examen
        Examen::create($validated);

        return response()->json(['message' => 'Examen créé avec succès'], 201);
    }
   public function index(Request $request)
    {
        // Récupérer tous les examens avec les relations
        $examens = Examen::with(['classroom', 'matiere', 'professeur'])
            ->when($request->class_id, function ($query, $class_id) {
                return $query->where('classe_id', $class_id);
            })
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        return response()->json($examens);
    }
    public function show($id)
    {
        // Récupérer l'examen par son ID avec les relations
        $examen = Examen::with(['classroom', 'matiere', 'professeur'])->find($id);

        // Si l'examen n'existe pas, renvoyer une erreur 404
        if (!$examen) {
            return response()->json(['message' => 'Examen non trouvé'], 404);
        }

        // Renvoyer les données de l'examen trouvé
        return response()->json($examen);
    }
    public function update(Request $request, $id)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'classe_id' => 'required|exists:classrooms,id',
            'matiere_id' => 'required|exists:matieres,id',
            'professeur_id' => 'nullable|exists:professeurs,id',
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i:s',
            'heure_fin' => 'required|date_format:H:i:s|after:heure_debut',
            'jour' => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche',
        ]);

        // Récupérer l'examen par son ID
        $examen = Examen::find($id);

        // Si l'examen n'existe pas, renvoyer une erreur 404
        if (!$examen) {
            return response()->json(['message' => 'Examen non trouvé'], 404);
        }

        // Mettre à jour l'examen avec les nouvelles données
        $examen->update($validated);

        return response()->json(['message' => 'Examen mis à jour avec succès']);
    }
    public function destroy($id)
    {
        // Récupérer l'examen par son ID
        $examen = Examen::find($id);

        // Si l'examen n'existe pas, renvoyer une erreur 404
        if (!$examen) {
            return response()->json(['message' => 'Examen non trouvé'], 404);
        }

        // Supprimer l'examen
        $examen->delete();

        return response()->json(['message' => 'Examen supprimé avec succès']);
    }
    public function emploiExamensEtudiant()
    {
        $user = Auth::user();
        $classe = $user->classe; // Supposant qu'un étudiant appartient à une classe
        
        $examens = Examen::with(['matiere', 'professeur'])
            ->where('classe_id', $classe->id)
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
            
        return view('examens.etudiant', compact('examens'));
    }
    
    // Pour l'API (React)
    public function getExamensEtudiant($etudiantId)
    {
        try {
            $etudiant = Etudiant::with('classroom')->findOrFail($etudiantId);
            
            if (!$etudiant->classroom) {  // Changé de 'classe' à 'classroom'
                return response()->json(['message' => 'Aucune classroom affectée'], 404);
            }
    
            $examens = Examen::with(['matiere', 'professeur'])
                ->where('classe_id', $etudiant->classroom->id)  // Ici on garde 'classe_id' si c'est le nom de la colonne
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->get();
    
            return response()->json($examens);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des examens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    }
