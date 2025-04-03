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
            'description' => 'nullable|string|max:1000',
            'capacite' => 'required|integer|min:1',
            'niveau' => 'required|string',
        ]);

        $classroom = Classroom::create([
            'name' => $request->name,
            'description' => $request->description,
            'capacite' => $request->capacite,
            'niveau' => $request->niveau,
        ]);

        return response()->json($classroom, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update([
            'name' => $request->name,
            'description' => $request->description,
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
        $classrooms = Classroom::all();
        return response()->json($classrooms);
    }

    public function show($id)
    {
        $classroom = Classroom::findOrFail($id);
        return response()->json($classroom);
    }
}
