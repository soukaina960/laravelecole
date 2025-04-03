<?php
namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'recipient_email' => 'required|email',
            'recipient_type' => 'required|in:etudiant,parent',
            'etudiant_id' => 'required|exists:etudiants,id',
        ]);

        // Ici vous pouvez implémenter la logique d'envoi réel
        // Pour l'exemple, nous retournons une réponse de succès

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
            'data' => [
                'recipient' => $request->recipient_email,
                'type' => $request->recipient_type,
                'etudiant_id' => $request->etudiant_id,
                'message_length' => strlen($request->message)
            ]
        ]);
    }
}