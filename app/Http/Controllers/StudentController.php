<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;


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

    public function index()
    {
        $etudiants = Etudiant::with('classroom')->get();

        $etudiants->each(function ($etudiant) {
            if ($etudiant->photo_profil) {
                $etudiant->photo_profil_url = asset('storage/' . $etudiant->photo_profil);
            }
        });

        return response()->json($etudiants);
    }




    public function show($id)
{
    $etudiant = Etudiant::with('classroom', 'professeurs')->findOrFail($id);

    if ($etudiant->photo_profil) {
        $etudiant->photo_profil_url = asset('storage/' . $etudiant->photo_profil);
    }

    return response()->json($etudiant);
}






    public function getEtudiantsParClasse($classeId)
{
    $etudiants = DB::table('etudiants')
        ->where('classe_id', $classeId)
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
    }}