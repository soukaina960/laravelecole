<?php
// app/Http/Controllers/CreneauController.php

namespace App\Http\Controllers;

use App\Models\Creneau;
use Illuminate\Http\Request;

class CreneauController extends Controller
{
    public function index()
    {
        return Creneau::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        return Creneau::create($validated);
    }

    public function update(Request $request, $id)
    {
        $creneau = Creneau::findOrFail($id);

        $validated = $request->validate([
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $creneau->update($validated);

        return response()->json($creneau, 200);
    }

    public function destroy($id)
    {
        $creneau = Creneau::findOrFail($id);
        $creneau->delete();

        return response()->json(['message' => 'Creneau deleted successfully'], 200);
    }
}
