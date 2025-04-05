<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        return response()->json(Incident::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $incident = Incident::create($request->all());

        return response()->json($incident, 201);
    }

    public function show($id)
    {
        return response()->json(Incident::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);
        $incident->update($request->all());

        return response()->json($incident);
    }

    public function destroy($id)
    {
        Incident::destroy($id);
        return response()->json(['message' => 'Incident supprimé']);
    }
}
