<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use Illuminate\Http\Request;


class RetardController extends Controller

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

