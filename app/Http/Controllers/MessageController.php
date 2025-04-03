<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    // Envoyer un message en masse
    public function sendBulkMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_ids' => 'required|array',
            'recipient_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $messages = [];
            foreach ($request->recipient_ids as $recipientId) {
                $messages[] = [
                    'sender_id' => auth()->id(),
                    'recipient_id' => $recipientId,
                    'subject' => $request->subject,
                    'content' => $request->content,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            Message::insert($messages);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Messages envoyés avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}