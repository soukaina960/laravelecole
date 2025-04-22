<?php
<<<<<<< HEAD
=======

>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD

=======
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'type_sanction',
        'description',
        'nombre_absences_min',
        'niveau_concerne',
    ];
    public function index()
{
    return response()->json(Sanction::all());
}

    public function createSanction($data)
    {
        return Sanction::create($data);
    }

    public function updateSanction($id, $data)
    {
        $sanction = Sanction::findOrFail($id);
        $sanction->update($data);
        return $sanction;
    }

    public function deleteSanction($id)
    {
        $sanction = Sanction::findOrFail($id);
        $sanction->delete();
        return response()->json(['message' => 'Sanction deleted successfully']);
    }
    public function getSanction($id)
    {
        return Sanction::findOrFail($id);
    }
    public function getSanctionsByLevel($niveau)
    {
        return Sanction::where('niveau_concerne', $niveau)->get();
    }
    public function getSanctionsByAbsences($nombre_absences)
    {
        return Sanction::where('nombre_absences_min', '<=', $nombre_absences)->get();
    }
    public function getSanctionsByType($type)
    {
        return Sanction::where('type_sanction', $type)->get();
    }
    public function getSanctionsByDescription($description)
    {
        return Sanction::where('description', 'LIKE', '%' . $description . '%')->get();
    }
    public function getSanctionsByDate($date)
    {
        return Sanction::whereDate('created_at', $date)->get();
    }
    public function getSanctionsByDateRange($start_date, $end_date)
    {
        return Sanction::whereBetween('created_at', [$start_date, $end_date])->get();
    }
    public function getSanctionsByStudent($etudiant_id)
    {
        return Sanction::where('etudiant_id', $etudiant_id)->get();
    }
    public function getSanctionsByClass($class_id)
    {
        return Sanction::where('class_id', $class_id)->get();
    }
    public function getSanctionsByTeacher($professeur_id)
    {
        return Sanction::where('professeur_id', $professeur_id)->get();
    }
}
=======
        'etudiant_id',
        'type',
        'description',
        'date',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}

>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
