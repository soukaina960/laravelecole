<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Etudiant;

class PaiementEnRetardNotification extends Notification
{
    use Queueable;

    protected $etudiant;
    protected $pourAdmin;

    public function __construct(Etudiant $etudiant, $pourAdmin = false)
    {
        $this->etudiant = $etudiant;
        $this->pourAdmin = $pourAdmin;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = new MailMessage;

        if ($this->pourAdmin) {
            $message->subject('Paiement en retard')
                    ->line("L'étudiant {$this->etudiant->nom} n'a pas payé ce mois.");
        } else {
            $message->subject('Rappel de paiement')
                    ->line("Bonjour {$this->etudiant->nom}, vous n'avez pas encore payé vos frais pour ce mois.");
        }

        return $message;
    }
}
