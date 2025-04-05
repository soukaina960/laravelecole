<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json(Notification::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'contenu' => 'required|string',
            'envoyee_par' => 'required|exists:utilisateurs,id',
        ]);

        $notification = Notification::create($request->all());

        return response()->json($notification, 201);
    }

    public function show($id)
    {
        return response()->json(Notification::findOrFail($id));
    }

    public function destroy($id)
    {
        Notification::destroy($id);
        return response()->json(['message' => 'Notification supprimée']);
    }
}
