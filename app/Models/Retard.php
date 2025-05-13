<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


<<<<<<< HEAD

class RetardController extends Controller
=======
class Retard extends Model

>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
{
    use HasFactory;
    protected $fillable = [
        'etudiant_id',
        'professeur_id',
        'class_id',
        'matiere_id',
        'date',
        'heure',
        'surveillant_id',
    ];
    
    protected $table = 'retards'; // Assurez-vous que la table est bien 'retards'



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
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }
    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'professeur_id');
    }
    public function surveillant()
    {
        return $this->belongsTo(Surveillant::class , 'surveillant_id');
    }
    



    





    }

