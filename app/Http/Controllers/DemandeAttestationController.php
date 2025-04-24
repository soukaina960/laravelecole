<?php
// app/Http/Controllers/Api/DemandeAttestationController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemandeAttestation;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DemandeAttestationController extends Controller
{
    // Étudiant crée une demande
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
        ]);

        $demande = DemandeAttestation::create([
            'etudiant_id' => $request->etudiant_id,
            'traitee' => false,
        ]);

        return response()->json(['message' => 'Demande envoyée avec succès', 'demande' => $demande]);
    }

    // Admin voit toutes les demandes
    public function index()
    {
        $demandes = DemandeAttestation::with('etudiant')->get();
        return response()->json($demandes);
    }

    // Admin traite la demande
    public function marquerCommeTraitee($id)
    {
        $demande = DemandeAttestation::findOrFail($id);
        $etudiantId = $demande->etudiant_id;

        $pdfUrl = url("/api/etudiants/{$etudiantId}/attestation-pdf");

        $demande->update([
            'traitee' => true,
            'lien_attestation' => $pdfUrl
        ]);

        return response()->json([
            'message' => 'Demande traitée avec succès',
            'lien' => $pdfUrl
        ]);
    }

    // Étudiant voit ses propres demandes
    public function demandesEtudiant($id)
    {
        $demandes = DemandeAttestation::where('etudiant_id', $id)->get();
        return response()->json($demandes);
    }
    public function getDemandesNonTraitees()
    {
        // Récupère toutes les demandes non traitées
        $demandes = DemandeAttestation::where('traitee', false)->get();
        return response()->json($demandes);
    }

    public function traiterDemande($id)
    {
        // Récupère la demande
        $demande = DemandeAttestation::findOrFail($id);
        $etudiant = Etudiant::findOrFail($demande->etudiant_id);

        // Traiter la demande (marquer comme traitée)
        $demande->traitee = true;
        $demande->save();

        // Générer le lien d'attestation (utilisation d'un PDF ou stockage d'un fichier)
        $pdf = PDF::loadView('attestation', ['etudiant' => $etudiant, 'config' => $this->getSchoolConfig()]);
        $filePath = 'attestations/' . 'attestation_' . $etudiant->matricule . '.pdf';
        $pdf->save(storage_path('app/public/' . $filePath));

        // Sauvegarder le lien dans la base de données
        $demande->lien_attestation = $filePath;
        $demande->save();

        // Retourner le lien de l'attestation générée
        return response()->json(['lien' => $filePath]);
    }

    // Méthode pour récupérer la configuration de l'école
    private function getSchoolConfig()
    {
        return (object)[
            'nom_ecole' => 'Institut Supérieur de Technologie Hay Salam',
            'telephone' => '0528223344',
            'fax' => '0528223345',
            'logo_path' => 'logos/logo.png', // Ajuster le chemin
        ];
    }

}
