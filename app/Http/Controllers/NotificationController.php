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
    // 🔔 Notifications d’un étudiant spécifique
public function getByEtudiant($etudiant_id)
{
    $notifications = Notification::where('etudiant_id', $etudiant_id)
                    ->with('etudiant')
                    ->get();

    return response()->json($notifications);
}

// 📤 Notifications envoyées par un utilisateur spécifique
public function getByEnvoyeur($user_id)
{
    $notifications = Notification::where('envoyee_par', $user_id)
                    ->with('etudiant')
                    ->get();

    return response()->json($notifications);
}

}