<?php
// app/Http/Controllers/Api/DemandeAttestationController.php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemandeAttestation;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConfigAttestation; // ✅ ajoute ceci


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
        $demande = DemandeAttestation::findOrFail($id);

        if ($demande->traitee) {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 400);
        }

        $etudiant = $demande->etudiant;

        $config = ConfigAttestation::first() ?? $this->getSchoolConfig(); // ✅ utilise getSchoolConfig

        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y') . '/' . (date('Y') + 1)
        ];
        // Avant Pdf::loadView(), dump les données :
            dd([
                'etudiant' => $etudiant,
                'config' => $config,
                'attestation' => $attestation
            ]);

        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation
        ])>setOption('enable-php', true);
        return $pdf->stream('attestation.pdf');
        $pdfPath = 'attestations/attestation_' . $etudiant->id . '_' . time() . '.pdf';

        Storage::disk('public')->put($pdfPath, $pdf->output());

        $demande->update([
            'traitee' => true,
            'lien_attestation' => $pdfPath
        ]);

        return response()->json([
            'message' => 'Demande traitée avec succès !',
            'lien' => asset('storage/' . $pdfPath)
        ]);
    }

    private function getSchoolConfig() // ✅ utilisé ici
    {
        return (object)[
            'nom_ecole' => 'Institut Supérieur de Technologie Hay Salam',
            'telephone' => '0528223344',
            'fax' => '0528223345',
            'logo_path' => 'logos/logo.png', // Ajuster le chemin
        ];
    }

}
