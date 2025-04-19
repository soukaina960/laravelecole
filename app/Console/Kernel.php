<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Paiement;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaiementEnRetardNotification;

class PaimentMenuels
{
    public function verifierPaiementMensuel()
    {
        $moisActuel = now()->format('Y-m');

        $etudiants = Etudiant::all();

        foreach ($etudiants as $etudiant) {
            $paiementEffectue = $etudiant->paiements()
                ->where('mois', $moisActuel)
                ->exists();

            if (!$paiementEffectue) {
                // Envoyer une notification à l'étudiant ET à l'admin
                $admin = \App\Models\User::where('role', 'admin')->first(); // adapte à ton système
                Notification::route('mail', $etudiant->email)
                    ->notify(new PaiementEnRetardNotification($etudiant));

                if ($admin) {
                    Notification::route('mail', $admin->email)
                        ->notify(new PaiementEnRetardNotification($etudiant, true));
                }
            }
        }
    }
}
