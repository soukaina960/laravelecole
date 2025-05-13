<?php

namespace App\Http\Controllers;

use App\Models\ConfigAttestation;
use Illuminate\Http\Request;

class ConfigAttestationController extends Controller
{
    // Récupérer la configuration actuelle
    public function index()
    {
        $config = ConfigAttestation::first(); // On suppose une seule configuration
        return response()->json($config);
    }

    // Créer une nouvelle configuration
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'nom_ecole' => 'required|string|max:255',
            'annee_scolaire' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:15',
            'fax' => 'nullable|string|max:15',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'cachet' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            // Créer ou mettre à jour la configuration
            $config = ConfigAttestation::first() ?: new ConfigAttestation();

            // Sauvegarder les fichiers et ajouter les chemins
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $config->logo_path = $logoPath;
            }

            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
                $config->signature_path = $signaturePath;
            }

            if ($request->hasFile('cachet')) {
                $cachetPath = $request->file('cachet')->store('cachets', 'public');
                $config->cachet_path = $cachetPath;
            }

            // Sauvegarder les autres données
            $config->nom_ecole = $validated['nom_ecole'];
            $config->annee_scolaire = $validated['annee_scolaire'];
            $config->telephone = $validated['telephone'];
            $config->fax = $validated['fax'];

            $config->save();

            return response()->json(['message' => 'Configuration mise à jour avec succès !'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de la configuration.'], 500);
        }
    }

    // Mettre à jour la configuration
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_ecole' => 'required|string|max:255',
            'nom_faculte' => 'nullable|string|max:255',
            'annee_scolaire' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cachet' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $config = ConfigAttestation::findOrFail($id);

        $config->nom_ecole = $request->nom_ecole;
        $config->nom_faculte = $request->nom_faculte;
        $config->annee_scolaire = $request->annee_scolaire;
        $config->telephone = $request->telephone;
        $config->fax = $request->fax;

        if ($request->hasFile('logo')) {
            $config->logo_path = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('signature')) {
            $config->signature_path = $request->file('signature')->store('signatures', 'public');
        }

        if ($request->hasFile('cachet')) {
            $config->cachet_path = $request->file('cachet')->store('cachets', 'public');
        }

        $config->save();

        return response()->json($config);
    }
}
