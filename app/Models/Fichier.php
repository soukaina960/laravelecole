<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    protected $fillable = [
        'professeur_id',
        'classe_id',
     
        'semestre_id',
        'type',
        'nom_fichier',
        'chemin_fichier',
    ];
    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'professeur_id');
    }   
    public function classe()
    {
        return $this->belongsTo(Classroom::class, 'classe_id');
    }
    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }
}

