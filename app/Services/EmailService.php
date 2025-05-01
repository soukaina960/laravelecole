<?php
// app/Services/EmailService.php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialsMail;

class EmailService
{
    /**
     * Envoie un email avec les informations de connexion.
     *
     * @param $utilisateur
     * @param $password
     * @return void
     */
    public function sendCredentials($utilisateur, $password)
    {
        Mail::to($utilisateur->email)->send(new CredentialsMail($utilisateur, $password));
    }
}
