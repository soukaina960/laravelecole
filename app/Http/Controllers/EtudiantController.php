<?php
// App\Http\Controllers\EtudiantController.php
namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom; // Assurez-vous d'importer le modèle Classroom


class EtudiantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // Assurer que l'utilisateur est authentifié
    }

    public function getEtudiantInfo(Request $request)
    {
        $userId = $request->user()->id;

        // Log de l'ID de l'utilisateur pour le debugging
        Log::info("ID de l'utilisateur : $userId");

        try {
            // Récupérer l'étudiant avec sa classe
            $etudiant = Etudiant::where('utilisateur_id', $userId)
                                ->with('classroom') // Charger la relation classroom
                                ->firstOrFail();  // Utiliser firstOrFail pour renvoyer une erreur 404 si pas trouvé
        } catch (\Exception $e) {
            // Log de l'erreur si l'étudiant n'est pas trouvé
            Log::error("Erreur lors de la récupération des informations de l'étudiant: " . $e->getMessage());
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        // Retourner les informations de l'étudiant sous forme de JSON
        return response()->json($etudiant);
    }
}
