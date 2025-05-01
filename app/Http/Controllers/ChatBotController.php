<?php

namespace App\Http\Controllers;
use App\Models\Faq;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function handle(Request $request)
    {
        $message = strtolower($request->message);
    
        // 1. Recherche dans les FAQs (base de données)
        $faq = Faq::all()->first(function ($item) use ($message) {
            return str_contains($message, strtolower($item->question));
        });
    
        if ($faq) {
            return response()->json(['reply' => $faq->answer]);
        }
    
        // 2. Sinon, logique par mots-clés (règles simples)
        if (str_contains($message, 'paiement')) {
            return response()->json(['reply' => "Allez dans Finances > Paiement."]);
        } elseif (str_contains($message, 'emploi du temps')) {
            return response()->json(['reply' => "Vous trouverez l’emploi du temps dans le menu dédié."]);
        }
    
        // 3. Réponse par défaut
        return response()->json(['reply' => "Je n’ai pas compris. Veuillez reformuler."]);
    }}
