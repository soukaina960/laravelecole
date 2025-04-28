<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialsMail;
use App\Models\Utilisateur;



// routes/web.php
use App\Http\Controllers\UtilisateurController;

Route::post('/envoyer-email', [UtilisateurController::class, 'envoyerMail']);

Route::get('/attestations/{id}/download', [StudentController::class, 'generateAttestation']);


