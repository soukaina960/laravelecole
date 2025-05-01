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
        'heure',
        'professeur_id',
        'class_id',
        'matiere_id',
    ];

    // Relation avec le modèle Etudiant
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    public function professeur()
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
}