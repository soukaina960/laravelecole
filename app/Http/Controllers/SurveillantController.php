<?php

namespace App\Http\Controllers;

use App\Models\Surveillant;
use Illuminate\Http\Request;

class SurveillantController extends Controller
{
    // 📄 Afficher tous les surveillants
    public function index()
    {
        // Utilise le 'role' pour filtrer les surveillants
        $surveillants = Surveillant::where('role', 'surveillant')->get();
        return response()->json($surveillants);
    }
    

    // ➕ Afficher le formulaire de création
    public function create()
    {
        return view('surveillants.create');
    }

    // ✅ Enregistrer un nouveau surveillant
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs',
            'matricule' => 'required|string|unique:utilisateurs',
            'mot_de_passe' => 'required|string|min:6',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            'photo_profil' => 'nullable|string',
        ]);

        $data['role'] = 'surveillant';
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);

        Surveillant::create($data);

        return redirect()->route('surveillants.index')->with('success', 'Surveillant ajouté avec succès !');
    }

    // 👁️ Afficher un seul surveillant + ses relations
    public function show($id)
    {
        $surveillant = Surveillant::with(['absences', 'retards', 'emploiSurveillant', 'sanctions', 'notifications', 'incidents'])
            ->findOrFail($id);

        return view('surveillants.show', compact('surveillant'));
    }

    // ✏️ Afficher le formulaire de modification
    public function edit($id)
    {
        $surveillant = Surveillant::findOrFail($id);
        return view('surveillants.edit', compact('surveillant'));
    }

    // 🔄 Mettre à jour les infos du surveillant
    public function update(Request $request, $id)
    {
        $surveillant = Surveillant::findOrFail($id);

        $data = $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs,email,' . $id,
            'matricule' => 'required|string|unique:utilisateurs,matricule,' . $id,
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            'photo_profil' => 'nullable|string',
        ]);

        $surveillant->update($data);

        return redirect()->route('surveillants.index')->with('success', 'Surveillant mis à jour avec succès !');
    }

    // 🗑️ Supprimer un surveillant
    public function destroy($id)
    {
        $surveillant = Surveillant::findOrFail($id);
        $surveillant->delete();

        return redirect()->route('surveillants.index')->with('success', 'Surveillant supprimé avec succès !');
    }
}