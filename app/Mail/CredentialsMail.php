<?php
// app/Mail/CredentialsMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class CredentialsMail extends Mailable
{
    public $utilisateur;
    public $password;

    public function __construct($utilisateur, $password)
    {
        $this->utilisateur = $utilisateur;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('emails.credentials')
                    ->with([
                        'utilisateur' => $this->utilisateur,
                        'password' => $this->password,
                    ]);
    }
}
