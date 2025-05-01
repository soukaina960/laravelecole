<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retard extends Model
{
    use HasFactory;

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
   
class RetardController extends Controller
{
    public function index()
    {
        return response()->json(Retard::with('etudiant')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'date' => 'required|date',
            'heure' => 'required|string',
        ]);

        $retard = Retard::create($request->all());
        return response()->json($retard, 201);
    }

    public function show($id)
    {
        return response()->json(Retard::findOrFail($id));
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
    }
}
