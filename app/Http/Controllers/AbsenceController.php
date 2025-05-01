<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Retard;
use App\Models\Etudiant;
use App\Models\ParentModel;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;

class AbsenceController extends Controller
{
    // Affiche toutes les absences
    public function index()
    {
        return response()->json(Absence::with('etudiant.classroom')->get());
    }

    // Enregistrer une absence
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'date' => 'required|date_format:Y-m-d',
            'justifiee' => 'required|string|in:oui,non',
            'motif' => 'required|string',
            'professeur_id' => 'required|exists:professeurs,id',
            'class_id' => 'required|exists:classrooms,id',
            'matiere_id' => 'required|exists:matieres,id',
        ]);

        $justifiee = ($validated['justifiee'] === 'oui') ? 1 : 0;

        $absence = Absence::create([
            'etudiant_id' => $validated['etudiant_id'],
            'date' => $validated['date'],
            'justifiee' => $justifiee,
            'professeur_id' => $validated['professeur_id'],
            'motif' => $validated['motif'],
            'class_id' => $validated['class_id'],
            'matiere_id' => $validated['matiere_id'],
        ]);

        return response()->json($absence, 201);
    }

    // Afficher une absence spécifique
    public function show($id)
    {
        return response()->json(Absence::with('etudiant.classroom')->findOrFail($id));
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
    public function getByEtudiant($id)
    {
        $absences = Absence::with(['professeur:id,nom'])
            ->where('etudiant_id', $id)
            ->get();

        return response()->json($absences);
    }

    // Absences d’un étudiant triées par date décroissante
    public function getByStudent($etudiantId)
    {
        $etudiant = Etudiant::find($etudiantId);
        if (!$etudiant) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        $absences = Absence::where('etudiant_id', $etudiantId)
            ->orderBy('date', 'desc')
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

    // Mes absences (pour un étudiant connecté)
    public function mesAbsences()
    {
        if (!Auth::user()->etudiant) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $absences = Auth::user()->etudiant->absences()
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($absences);
    }

    // Envoyer une notification par email au parent
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
            $mail->Username = 'aitouhlalfarah18@gmail.com';
            $mail->Password = 'csfbjnjcukhhtbvh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('aitouhlalfarah18@gmail.com', 'Absence Notification');
            $mail->addAddress($parent->email);
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
    public function getAbsencesByParentId($parentId)
    {
        // Vérifie si le parent_id est présent dans l'URL
        if (!$parentId) {
            return response()->json(['message' => 'parent_id manquant'], 400);
        }
    
        // Récupère les absences où le parent_id correspond
        $absences = Absence::whereHas('etudiant', function ($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })
        ->with(['etudiant', 'classroom', 'matiere', 'professeur']) // <= هنا زدنا class و matiere و professeur
        ->get();
    
        // Si aucune absence n'est trouvée
        if ($absences->isEmpty()) {
            return response()->json(['message' => 'Aucune absence trouvée pour ce parent_id'], 404);
        }
    
        // Retourne les absences sous forme de JSON
        return response()->json($absences);
    }
    
}
