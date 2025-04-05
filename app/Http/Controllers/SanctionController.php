<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    public function index()
    {
        return response()->json(Sanction::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $sanction = Sanction::create($request->all());

        return response()->json($sanction, 201);
    }

    public function show($id)
    {
        return response()->json(Sanction::findOrFail($id));
    }

    public function destroy($id)
    {
        Sanction::destroy($id);
        return response()->json(['message' => 'Sanction supprimée']);
    }
}
