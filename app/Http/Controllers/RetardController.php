<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use Illuminate\Http\Request;

class RetardController extends Controller
{
    public function index()
    {
        return response()->json(Retard::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'date' => 'required|date',
            'heure' => 'required',
        ]);

        $retard = Retard::create($request->all());

        return response()->json($retard, 201);
    }

    public function show($id)
    {
        return response()->json(Retard::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $retard = Retard::findOrFail($id);
        $retard->update($request->all());

        return response()->json($retard);
    }

    public function destroy($id)
    {
        Retard::destroy($id);
        return response()->json(['message' => 'Retard supprimé']);
    }
}
