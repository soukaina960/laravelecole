<?php
// app/Http/Controllers/Api/DemandeAttestationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemandeAttestation;
use App\Models\Etudiant;

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
}
