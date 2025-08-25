<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Professeur;
use App\Models\Utilisateur;
use App\Models\ConfigAttestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    // Create a new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants,email',
            'matricule' => 'required|string|unique:etudiants,matricule',
            'origine' => 'nullable|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string',
            'classe_id' => 'required|exists:classes,id',
            'montant_a_payer' => 'nullable|numeric',
            'photo_profil' => 'nullable|image|max:2048',
            'utilisateur_id' => 'nullable|exists:utilisateurs,id',
            'parent_id' => 'nullable|exists:utilisateurs,id',
            'professeurs' => 'nullable|array',
            'professeurs.*' => 'exists:professeurs,id',
        ]);

        $data = $request->only([
            'nom', 'prenom', 'email', 'matricule', 'origine', 'date_naissance',
            'sexe', 'adresse', 'classe_id', 'montant_a_payer', 'utilisateur_id', 'parent_id'
        ]);

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('photos/etudiants', 'public');
        }

        $etudiant = Etudiant::create($data);

        if ($request->has('professeurs')) {
            $etudiant->professeurs()->attach($validated['professeurs']);
        }

        return response()->json([
            'message' => 'Étudiant créé avec succès',
            'data' => $etudiant->load('classroom', 'professeurs')
        ], 201);
    }

    // Get all students with optional relationships
    public function index(Request $request)
    {
        $query = Etudiant::with(['classroom']);

        // Include additional relationships if requested
        if ($request->has('include')) {
            $includes = explode(',', $request->include);
            
            if (in_array('professeurs', $includes)) {
                $query->with('professeurs');
            }
        }

        $etudiants = $query->get()->map(function ($etudiant) {
            // Convert fields to UTF-8
            foreach ($etudiant->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $etudiant->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }

            // Handle classroom encoding if loaded
            if ($etudiant->relationLoaded('classroom') && $etudiant->classroom) {
                foreach ($etudiant->classroom->getAttributes() as $key => $value) {
                    if (is_string($value)) {
                        $etudiant->classroom->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
            }

            // Add profile photo URL
            $etudiant->photo_profil_url = $etudiant->photo_profil 
                ? asset('storage/' . $etudiant->photo_profil)
                : asset('storage/defaults/student.png');

            return $etudiant;
        });

        return response()->json($etudiants);
    }

    // Get a single student
   public function show($id)
{
    $etudiant = Etudiant::with(['classroom', 'professeurs'])->findOrFail($id);

    // Forcer l'encodage UTF-8 des attributs de l'étudiant
    foreach ($etudiant->getAttributes() as $key => $value) {
        if (is_string($value)) {
            $etudiant->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
    }

    // Forcer l'encodage des attributs de la classe
    if ($etudiant->relationLoaded('classroom') && $etudiant->classroom) {
        foreach ($etudiant->classroom->getAttributes() as $key => $value) {
            if (is_string($value)) {
                $etudiant->classroom->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        }
    }

    // Forcer l'encodage des professeurs
    if ($etudiant->relationLoaded('professeurs')) {
        foreach ($etudiant->professeurs as $professeur) {
            foreach ($professeur->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $professeur->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
        }
    }

    // Ajouter l'URL de la photo
    $etudiant->photo_profil_url = $etudiant->photo_profil 
        ? asset('storage/' . $etudiant->photo_profil)
        : asset('storage/defaults/student.png');

    return response()->json($etudiant);
}


    // Get students by class
    public function getEtudiantsParClasse($classeId)
    {
        $etudiants = Etudiant::where('classe_id', $classeId)
            ->with('classroom')
            ->get();

        return response()->json($etudiants);
    }

   public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email',
            'matricule' => 'required|string',
            'origine' => 'nullable|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string',
            'classe_id' => 'required|exists:classrooms,id',
            'montant_a_payer' => 'nullable|numeric',
            'utilisateur_id' => 'nullable|exists:utilisateurs,id',
            'parent_id' => 'nullable|exists:utilisateurs,id',
            'photo_profil' => 'nullable|image|max:2048',
            'professeurs' => 'nullable|array',
        ]);

        $etudiant = Etudiant::findOrFail($id);

        $data = $request->only([
            'nom', 'prenom', 'email', 'matricule', 'origine', 'date_naissance',
            'sexe', 'adresse', 'classe_id', 'montant_a_payer', 'utilisateur_id', 'parent_id'
        ]);

        if ($request->hasFile('photo_profil')) {
            // Supprimer l’ancienne si elle existe
            if ($etudiant->photo_profil) {
                Storage::disk('public')->delete($etudiant->photo_profil);
            }
            $data['photo_profil'] = $request->file('photo_profil')->store('photos', 'public');
        }

        $etudiant->update($data);

        if ($request->has('professeurs')) {
            $etudiant->professeurs()->sync($request->professeurs);
        }

        return response()->json($etudiant);
    }
    // Delete a student
    public function destroy($id)
    {
        $etudiant = Etudiant::findOrFail($id);

        // Delete profile photo if exists
        if ($etudiant->photo_profil) {
            Storage::disk('public')->delete($etudiant->photo_profil);
        }

        $etudiant->delete();

        return response()->json([
            'message' => 'Étudiant supprimé avec succès'
        ], 204);
    }

    // Assign professors to a student
    public function affecterProfesseurs(Request $request, $etudiantId)
    {
        $validated = $request->validate([
            'professeurs' => 'required|array',
            'professeurs.*' => 'exists:professeurs,id',
        ]);

        $etudiant = Etudiant::findOrFail($etudiantId);
        $etudiant->professeurs()->sync($validated['professeurs']);

        return response()->json([
            'message' => 'Professeurs affectés avec succès',
            'data' => $etudiant->load('professeurs')
        ]);
    }

    // Generate attestation PDF
    public function generateAttestation($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $config = ConfigAttestation::first() ?? $this->getDefaultConfig();
        
        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y').'/'.(date('Y')+1)
        ];
        
        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation
        ]);
        
        return $pdf->stream("attestation_{$etudiant->nom}.pdf");
    }

    // Get students by parent
    public function getEtudiantsByParent($parentId)
    {
        $etudiants = Etudiant::where('parent_id', $parentId)
            ->with('classroom')
            ->get();

        return response()->json($etudiants);
    }

    // Default configuration for attestation
    private function getDefaultConfig()
    {
        return (object)[
            'nom_ecole' => 'Université Hassan 1er',
            'nom_faculte' => 'Faculté des Sciences et Techniques de Settat',
            'annee_scolaire' => date('Y').'/'.(date('Y')+1),
            'telephone' => '',
            'fax' => '',
            'logo_path' => ''
        ];
    }
}