<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Etudiant;

class RetardPaiementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Etudiant $etudiant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Retard de paiement',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.retard',
            with: ['etudiant' => $this->etudiant],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
