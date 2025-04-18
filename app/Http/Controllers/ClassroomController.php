<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'niveau' => 'required|string',
            'filiere_id' => 'nullable|exists:filieres,id' // rendu facultatif
        ]);

        $classroom = Classroom::create([
            'name' => $request->name,
            'capacite' => $request->capacite,
            'niveau' => $request->niveau,
             'filiere_id' => $request->input('filiere_id')
        ]);

        return response()->json($classroom, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'niveau' => 'required|string',
            'filiere_id' => 'nullable|exists:filieres,id' // rendu facultatif
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update([
            'name' => $request->name,
            'capacite' => $request->capacite,
            'niveau' => $request->niveau,
            'filiere_id' => $request->input('filiere_id')

            ]);

        return response()->json($classroom);
    }

    public function destroy($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();
        return response()->json(null, 204);
    }

    public function index()
    {
        $classrooms = Classroom::with('filiere:id,nom')->get();
        return response()->json($classrooms);
    }

    public function show($id)
    {
        $classroom = Classroom::find($id);
        if (!$classroom) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }
        return response()->json($classroom);
    }
}
