<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Paiement;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaiementEnRetardNotification;

$schedule->call(function () {
    \App\Http\Controllers\PaiementMensuelController::resetPaiementsMoisPrecedent();
})->monthlyOn(1, '00:00'); // Réinitialise les paiements tous les premiers du mois à minuit

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
