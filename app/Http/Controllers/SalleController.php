<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salle;

class SalleController extends Controller
{
    public function index()
    {
        return response()->json(Salle::all());
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string']);
        $salle = Salle::create(['nom' => $request->nom]);
        return response()->json($salle);
    }

    public function update(Request $request, $id)
    {
        $salle = Salle::find($id);
        if (!$salle) return response()->json(['message' => 'Salle non trouvée'], 404);

        $request->validate(['nom' => 'required|string']);
        $salle->nom = $request->nom;
        $salle->save();

        return response()->json($salle);
    }

    public function destroy($id)
    {
        $salle = Salle::find($id);
        if (!$salle) return response()->json(['message' => 'Salle non trouvée'], 404);

        $salle->delete();
        return response()->json(['message' => 'Salle supprimée']);
    }
}
