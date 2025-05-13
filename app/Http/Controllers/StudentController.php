<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
<<<<<<< HEAD


















































=======
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Professeur;
use App\Models\ParentModel; 
use App\Models\Classe;
use App\Models\Utilisateur;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    
    
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants,email',
            'matricule' => 'required|string|unique:etudiants,matricule',
            'origine' => 'nullable|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string',
            'classe_id' => 'required|exists:classrooms,id',
            'montant_a_payer' => 'nullable|numeric',
            'photo_profil' => 'nullable|image|max:2048',
            'utilisateur_id' => 'nullable|exists:utilisateurs,id',
            'parent_id' => 'nullable|exists:utilisateurs,id',
            'professeurs' => 'nullable|array',
        ]);

        $data = $request->only([
            'nom', 'prenom', 'email', 'matricule', 'origine', 'date_naissance',
            'sexe', 'adresse', 'classe_id', 'montant_a_payer', 'utilisateur_id', 'parent_id'
        ]);

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('photos', 'public');
        }

        $etudiant = Etudiant::create($data);

        if ($request->has('professeurs')) {
            $etudiant->professeurs()->attach($request->professeurs);
        }

        return response()->json($etudiant, 201);
    }

<<<<<<< HEAD
    public function index()
    {
        $etudiants = Etudiant::with('classroom')->get();
    
        $etudiants = $etudiants->map(function ($etudiant) {
            // Convertir les champs de l'étudiant principal
            foreach ($etudiant->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $etudiant->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
    
            // Convertir les champs de la relation classroom
            if ($etudiant->relationLoaded('classroom') && $etudiant->classroom) {
                foreach ($etudiant->classroom->getAttributes() as $key => $value) {
                    if (is_string($value)) {
                        $etudiant->classroom->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
            }
    
            return $etudiant;
        });
    
        return response()->json($etudiants, 200, [], JSON_UNESCAPED_UNICODE);
    }
     
    
=======
    public function index(Request $request)
{
    // Initialise la requête avec classroom
    $query = Etudiant::with('classroom');
    
    // Vérifie si on doit inclure les professeurs
    if ($request->has('include')) {
        $includes = explode(',', $request->include);
        
        if (in_array('professeurs', $includes)) {
            $query->with('professeurs');
        }
    }





































    
    // Exécute la requête
    $etudiants = $query->get();
    
    // Ajoute les URLs des photos
    $etudiants->each(function ($etudiant) {
        $etudiant->photo_profil_url = $etudiant->photo_profil 
            ? asset('storage/' . $etudiant->photo_profil)
            : asset('storage/default_image.png');
    });
    
    return response()->json($etudiants);
}




>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11

    public function show($id)
{
    $etudiant = Etudiant::with('classroom', 'professeurs')->findOrFail($id);

    if ($etudiant->photo_profil) {
        $etudiant->photo_profil_url = asset('storage/' . $etudiant->photo_profil);
    }

    return response()->json($etudiant);
}




<<<<<<< HEAD
=======












































>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
    public function getEtudiantsParClasse($classeId)
{
    $etudiants = DB::table('etudiants')
        ->where('classe_id', $classeId)
        ->get();

    return response()->json($etudiants);
}


<<<<<<< HEAD
=======







































>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
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

    public function destroy($id)
    {
        $etudiant = Etudiant::findOrFail($id);

        if ($etudiant->photo_profil) {
            Storage::disk('public')->delete($etudiant->photo_profil);
        }

        $etudiant->delete();

        return response()->json(['message' => 'Étudiant supprimé avec succès!']);
    }

    public function affecterProfesseurs(Request $request, $etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        $request->validate([
            'professeurs' => 'required|array',
            'professeurs.*' => 'exists:professeurs,id',
        ]);

        $etudiant->professeurs()->sync($request->professeurs);

        return response()->json(['message' => 'Professeurs affectés avec succès!']);
    }

    public function generateAttestation($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        
        // Get the first config attestation or use default values
        $config = \App\Models\ConfigAttestation::first() ?? $this->getDefaultConfig();
        
        // Create a temporary attestation array with current date
        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y').'/'.(date('Y')+1)
        ];
        
        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation // Convert array to object for view compatibility
        ]);
        
        return $pdf->stream("attestation_{$etudiant->nom}.pdf");    }
    
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
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
    public function getEtudiantsByParent($parentId)
    {
        $etudiants = Etudiant::where('parent_id', $parentId)->get();
        return response()->json($etudiants);
    }
    
}