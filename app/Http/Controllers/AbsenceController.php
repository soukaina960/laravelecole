<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Retard;
use App\Models\Etudiant;
use App\Models\ParentModel;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Ajoutez cette ligne pour importer la classe Log

class AbsenceController extends Controller
{
    // Affiche toutes les absences
    public function index()
    {
        $absences = Absence::with('etudiant.classroom')->get();
        return response()->json($absences);
    }

    // Enregistrer une absence
    public function store(Request $request)
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'date' => 'required|date_format:Y-m-d',  // Assurer le format de la date
            'justifiee' => 'required|string|in:oui,non',
            'professeur_id' => 'required|exists:professeurs,id',
            'motif' => 'required|string',
            'class_id' => 'required|exists:classrooms,id', 
        ]);
        
        // Convert 'oui' => 1 and 'non' => 0 before saving
        $justifiee = ($validated['justifiee'] === 'oui') ? 1 : 0;
        
        // Now you can save the data with the correct value for 'justifiee'
        Absence::create([
            'etudiant_id' => $validated['etudiant_id'],
            'date' => $validated['date'],
            'justifiee' => $justifiee,
            'professeur_id' => $validated['professeur_id'],
            'motif' => $validated['motif'],
            'class_id' => $validated['class_id'], // Assuming you have this in the request
        ]);
        
    }

    // Afficher une absence spécifique
    public function show($id)
    {
        $absence = Absence::with('etudiant.classroom')->findOrFail($id);
        return response()->json($absence);
    }

    // Mettre à jour une absence
    public function update(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        $absence->update($request->all());
        return response()->json($absence);
    }

    // Supprimer une absence
    public function destroy($id)
    {
        Absence::destroy($id);
        return response()->json(['message' => 'Absence supprimée']);
    }

    // Absences d'un étudiant
    public function getByEtudiant($etudiant_id)
    {
        $absences = Absence::where('etudiant_id', $etudiant_id)
                        ->with('etudiant.classroom')
                        ->get();

        return response()->json($absences);
    }

    // Absences entre deux dates
    public function getByDateRange($etudiant_id, $date_debut, $date_fin)
    {
        $absences = Absence::where('etudiant_id', $etudiant_id)
                        ->whereBetween('date', [$date_debut, $date_fin])
                        ->with('etudiant.classroom')
                        ->get();

        return response()->json($absences);
    }
    public function notifyParent($etudiantId)
    {
      
        $etudiant = Etudiant::find($etudiantId);
        
        
        if (!$etudiant) {
            return response()->json(['status' => 'error', 'message' => 'Étudiant non trouvé'], 404);
        }
        
        
        $parent = ParentModel::find($etudiant->parent_id);
        
      
        if (!$parent) {
            return response()->json(['status' => 'error', 'message' => 'Parent non trouvé'], 404);
        }

       
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // الإيميل المرسل
            $mail->Password = 'your-email-password';  // كلمة المرور للإيميل
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('your-email@gmail.com', 'Absence Notification');
            $mail->addAddress($parent->email);  // إيميل الأب
            $mail->Subject = "Notification d'absence";
            $mail->Body = "Cher parent, votre enfant a accumulé des absences. Merci de vérifier.";

            if ($mail->send()) {
                return response()->json(['status' => 'success', 'message' => 'Email envoyé avec succès']);
            } else {
                return response()->json(['status' => 'error', 'message' => $mail->ErrorInfo]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()]);
        }
    }
}
