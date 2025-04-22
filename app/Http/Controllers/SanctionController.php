<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    // Afficher toutes les sanctions
    public function index()
    {
        $sanctions = Sanction::all();
        return response()->json($sanctions);
    }
    

    // Afficher le formulaire de création
    public function create()
    {
        return view('sanctions.create');
    }

    // Enregistrer une nouvelle sanction
    public function store(Request $request)
    {
        $request->validate([
            'type_sanction' => 'required',
            'description' => 'required',
            'nombre_absences_min' => 'required|integer',
            'niveau_concerne' => 'required',
        ]);

        Sanction::create($request->all());

        return redirect('/sanctions')->with('success', 'Sanction ajoutée avec succès !');
    }
}

