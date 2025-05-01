<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\ParentModel;
use App\Models\Paiement;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class RetardPaiementController extends Controller
{
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

    public function envoyerNotification($id)
    {
        $etudiant = Etudiant::with('parentModel')->findOrFail($id); // Utilisez 'parentModel' si c'est le nom de votre relation
        
        if (!$etudiant->parentModel) {
            Log::error('Aucun parent associé à l\'étudiant: ' . $etudiant->id);
            return response()->json(['message' => 'Aucun parent associé à cet étudiant.'], 400);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aitouhlalfarah18@gmail.com';
            $mail->Password = 'csfbjnjcukhhtbvh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                Log::info("PHPMailer Debug: $str");
            };

            $mail->setFrom('aitouhlalfarah18@gmail.com', 'Administration École');
            $mail->addAddress($etudiant->parentModel->email, $etudiant->parentModel->prenom);
            $mail->Subject = 'Rappel de paiement - École privée';
            $mail->Body = "Bonjour M./Mme {$etudiant->parentModel->nom},\n\nVous avez un retard de paiement pour le mois en cours concernant votre enfant {$etudiant->prenom} {$etudiant->nom}. Merci de régulariser votre situation dans les plus brefs délais.\n\nCordialement,\nL'administration de l'école.";

            if (!$mail->send()) {
                Log::error('Échec de l\'envoi de l\'e-mail. Erreur : ' . $mail->ErrorInfo);
                return response()->json(['message' => 'Échec de l\'envoi de l\'e-mail.'], 500);
            }

            Log::info('Email de rappel envoyé au parent: ' . $etudiant->parentModel->email);
            return response()->json(['message' => 'Notification envoyée au parent.']);

        } catch (Exception $e) {
            Log::error("Erreur lors de l'envoi de l'e-mail : " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'envoi de l\'e-mail.'], 500);
        }
    }
}