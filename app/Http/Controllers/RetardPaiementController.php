<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RetardPaiementController extends Controller
{
    // ✅ Récupère la liste des étudiants qui n'ont pas payé pour le mois en cours
    public function index()
    {
        $moisActuel = date('m');
        $anneeActuelle = date('Y');

        $etudiants = Etudiant::whereDoesntHave('paiements', function ($query) use ($moisActuel, $anneeActuelle) {
            $query->whereMonth('mois', $moisActuel)
                  ->whereYear('mois', $anneeActuelle);
        })->get();

        return response()->json($etudiants);
    }

    // ✅ Envoie un email de rappel à un étudiant
    public function envoyerNotification($id)
    {
        $etudiant = Etudiant::findOrFail($id);

        // Tu peux personnaliser cet email selon ton design
        Mail::raw("Bonjour {$etudiant->prenom}, vous avez un retard de paiement pour le mois en cours. Merci de régulariser votre situation.", function ($message) use ($etudiant) {
            $message->to($etudiant->email)
                    ->subject("Rappel de paiement - École privée");
        });

        return response()->json(['message' => 'Notification envoyée.']);
    }
}
