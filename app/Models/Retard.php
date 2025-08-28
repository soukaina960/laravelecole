<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retard extends Model
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
        'motif' // Ajouté comme champ optionnel
    ];

    // Relation avec l'étudiant
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    // Relation avec le professeur
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }

    // Relation avec la classe
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    // Relation avec la matière
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    // Relation avec le surveillant
    public function surveillant()
    {
        return $this->belongsTo(Surveillant::class);
    }
}