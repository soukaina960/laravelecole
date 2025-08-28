<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\DemandeAttestation;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConfigAttestation;
use Illuminate\Validation\Rule;

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

        return response()->json([
            'message' => 'Demande envoyée avec succès', 
            'demande' => $demande
        ], 201);
    }

    // Admin voit toutes les demandes
    public function index()
    {
        $demandes = DemandeAttestation::with('etudiant')->get();
    
        $demandes = $demandes->map(function ($demande) {
            // Clean main model attributes
            foreach ($demande->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $demande->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
    
            // Clean related etudiant data
            if ($demande->relationLoaded('etudiant')) {
                foreach ($demande->etudiant->getAttributes() as $key => $value) {
                    if (is_string($value)) {
                        $demande->etudiant->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
            }
    
            return $demande;
        });
    
        return response()->json($demandes);
    }

    // Admin traite la demande
    public function marquerCommeTraitee($id)
    {
        return $this->traiterDemande($id);
    }

    // Étudiant voit ses propres demandes
    public function demandesEtudiant($id)
    {
        $demandes = DemandeAttestation::where('etudiant_id', $id)->get();
        return response()->json($demandes);
    }

    // Récupérer les demandes non traitées
    public function getDemandesNonTraitees()
    {
        $demandes = DemandeAttestation::where('traitee', false)
            ->with('etudiant')
            ->get();
            
        return response()->json($demandes);
    }

    // Traiter la demande et générer l'attestation en PDF
    public function traiterDemande($id)
    {
        $demande = DemandeAttestation::findOrFail($id);

        if ($demande->traitee) {
            return response()->json([
                'message' => 'Cette demande a déjà été traitée.'
            ], 400);
        }

        $etudiant = $demande->etudiant;
        $config = ConfigAttestation::first() ?? $this->getSchoolConfig();

        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y') . '/' . (date('Y') + 1),
        ];

        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation,
        ])->setOption('enable-php', true);

        $pdfPath = 'attestations/attestation_' . $etudiant->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        $demande->update([
            'traitee' => true,
            'lien_attestation' => $pdfPath, // Store relative path
        ]);

        return response()->json([
            'message' => 'Demande traitée avec succès',
            'lien' => asset('storage/' . $pdfPath)
        ]);
    }

    // Configuration par défaut de l'école
    private function getSchoolConfig()
    {
        return (object)[
            'nom_ecole' => 'Institut Supérieur de Technologie Hay Salam',
            'telephone' => '0528223344',
            'fax' => '0528223345',
            'logo_path' => 'logos/logo.png',
            'annee_scolaire' => date('Y') . '/' . (date('Y') + 1),
        ];
    }
}