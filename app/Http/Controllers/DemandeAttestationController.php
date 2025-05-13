<?php

namespace App\Http\Controllers;
<<<<<<< HEAD

=======
use Illuminate\Support\Facades\Log;
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemandeAttestation;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConfigAttestation; // ✅ ajoutée pour la configuration de l'attestation
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

        return response()->json(['message' => 'Demande envoyée avec succès', 'demande' => $demande]);
    }

    // Admin voit toutes les demandes
    public function index()
    {
        // Récupérer les demandes avec la relation "etudiant"
        $demandes = DemandeAttestation::with('etudiant')->get();
    
        // Nettoyage sécurisé des caractères mal encodés
        $demandes = $demandes->map(function ($demande) {
            foreach ($demande->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    // Convertit uniquement les chaînes en UTF-8 proprement
                    $demande->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
    
            // Nettoyer aussi les relations imbriquées (ex. : 'etudiant')
            if ($demande->relationLoaded('etudiant')) {
                foreach ($demande->etudiant->getAttributes() as $key => $value) {
                    if (is_string($value)) {
                        $demande->etudiant->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
            }
    
            return $demande;
        });
    
        // Retourner les données encodées correctement en JSON
        return response()->json($demandes);
    }

    // Admin traite la demande
    public function marquerCommeTraitee($id)
    {
        $demande = DemandeAttestation::findOrFail($id);
        $etudiant = $demande->etudiant;
    
        if ($demande->traitee) {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 400);
        }
    
        // Récupérer la configuration de l'école
        $config = ConfigAttestation::first() ?? $this->getSchoolConfig();
    
        // Créer les données de l'attestation
        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y') . '/' . (date('Y') + 1),
        ];
    
        // Générer le PDF
        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation,
        ])->setOption('enable-php', true);
    
        // Sauvegarder le PDF dans le stockage public
        $pdfPath = 'attestations/attestation_' . $etudiant->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
    
        // Mettre à jour la demande
        $demande->update([
            'traitee' => true,
            'lien_attestation' => $pdfPath, // Store just the path, not full URL
        ]);
    
        return response()->json([
            'message' => 'Demande traitée avec succès',
            'lien' => asset('storage/' . $pdfPath)
        ]);
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
        $demandes = DemandeAttestation::where('traitee', false)->get();
        return response()->json($demandes);
    }

    // Traiter la demande et générer l'attestation en PDF
    public function traiterDemande($id)
{
    $demande = DemandeAttestation::findOrFail($id);

<<<<<<< HEAD
        if ($demande->traitee) {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 400);
        }

        $etudiant = $demande->etudiant;

        // Récupérer la configuration de l'école
        $config = ConfigAttestation::first() ?? $this->getSchoolConfig();

        // Créer les données de l'attestation
        $attestation = [
            'date_emission' => now()->format('d/m/Y'),
            'annee_universitaire' => $config->annee_scolaire ?? date('Y') . '/' . (date('Y') + 1),
        ];

        // Générer le PDF à partir de la vue
        $pdf = Pdf::loadView('pdf.attestation', [
            'etudiant' => $etudiant,
            'config' => $config,
            'attestation' => (object)$attestation,
        ])->setOption('enable-php', true);

        // Sauvegarder le PDF dans le stockage public
        $pdfPath = 'attestations/attestation_' . $etudiant->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Mettre à jour la demande comme traitée et ajouter le lien vers le PDF
        $demande->update([
            'traitee' => true,
            'lien_attestation' => asset('storage/' . $pdfPath),
        ]);

        // Retourner la réponse avec le lien de l'attestation
        return response()->json([
            'message' => 'Demande traitée avec succès !',
            'lien' => asset('storage/' . $pdfPath),
        ]);
    }

    // Fonction privée pour obtenir la configuration de l'école
    private function getSchoolConfig()
=======
    if ($demande->traitee) {
        return response()->json(['message' => 'Cette demande a déjà été traitée.'], 400);
    }

    $etudiant = $demande->etudiant;

    $config = ConfigAttestation::first() ?? $this->getSchoolConfig();

    $attestation = [
        'date_emission' => now()->format('d/m/Y'),
        'annee_universitaire' => $config->annee_scolaire ?? date('Y') . '/' . (date('Y') + 1)
    ];

    // Générer le PDF
    $pdf = Pdf::loadView('pdf.attestation', [
        'etudiant' => $etudiant,
        'config' => $config,
        'attestation' => (object)$attestation
    ])->setOption('enable-php', true); // ✅ correction ici

    // Enregistrer le PDF dans le disque public
    $pdfPath = 'attestations/attestation_' . $etudiant->id . '_' . time() . '.pdf';
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // Marquer la demande comme traitée
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
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
    {
        return (object)[
            'nom_ecole' => 'Institut Supérieur de Technologie Hay Salam',
            'telephone' => '0528223344',
            'fax' => '0528223345',
            'logo_path' => 'logos/logo.png', // Ajuster le chemin du logo
        ];
    }
}
