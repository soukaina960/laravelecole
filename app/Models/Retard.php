<?php







namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


<<<<<<< HEAD
class RetardController extends Controller
{

=======
    // Champs qu'on peut remplir via create() ou update()
    protected $fillable = [
        'etudiant_id',
        'date',
        'heure'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);

    }
   





}





class RetardController extends Controller
{


>>>>>>> e8fd732 (Normalize)
    public function index()
    {
        return $this->belongsTo(Professeur::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
    public function matiere()
    {
        return $this->belongsTo(Matiere::class , 'matiere_id');
    }



    public function update(Request $request, $id)
    {
        $retard = Retard::findOrFail($id);
        $retard->update($request->all());

        return response()->json($retard);
    }

    public function destroy($id)
    {
        Retard::destroy($id);
        return response()->json(['message' => 'Retard supprimé']);
    }

    // ✅ Personnalisée : retards d’un étudiant
    public function getByEtudiant($etudiant_id)
    {
        $retards = Retard::where('etudiant_id', $etudiant_id)
                    ->with('etudiant')
                    ->get();

        return response()->json($retards);
    }

    // ✅ Personnalisée : retards d’un étudiant entre deux dates
    public function getByDateRange($etudiant_id, $date_debut, $date_fin)
    {
        $retards = Retard::where('etudiant_id', $etudiant_id)
                    ->whereBetween('date', [$date_debut, $date_fin])
                    ->with('etudiant')
                    ->get();

        return response()->json($retards);

<<<<<<< HEAD

=======
>>>>>>> e8fd732 (Normalize)
    }
}
