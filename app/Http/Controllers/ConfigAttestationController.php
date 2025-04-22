<?php

namespace App\Http\Controllers;

use App\Models\ConfigAttestation;
use Illuminate\Http\Request;

class ConfigAttestationController extends Controller
{
    // Récupérer la configuration actuelle
    public function index()
    {
        $config = ConfigAttestation::first(); // Supposons qu’il y ait une seule configuration globale
        return response()->json($config);
    }
// Créer une nouvelle configuration (au cas où aucune n'existe)
public function store(Request $request)
{
    $request->validate([
        'nom_ecole' => 'required|string|max:255',
        'annee_scolaire' => 'required|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'fax' => 'nullable|string|max:20',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = $request->all();

    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $data['logo_path'] = $logoPath;
    }

    $config = ConfigAttestation::create($data);

    return response()->json($config);
}

    // Mettre à jour la configuration de l'attestation
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_ecole' => 'required|string|max:255',
            'annee_scolaire' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $config = ConfigAttestation::findOrFail($id);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $config->logo_path = $logoPath;
        }
     $config->nom_ecole = $request->nom_ecole;
        $config->nom_faculte = $request->nom_faculte;
        $config->annee_scolaire = $request->annee_scolaire;
        $config->telephone = $request->telephone;
        $config->fax = $request->fax;

        $config->save();

        return response()->json($config);
    }
}
