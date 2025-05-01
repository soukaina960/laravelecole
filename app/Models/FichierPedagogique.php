<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichierPedagogique extends Model
{
    // Specify the correct table name
    protected $table = 'fichiers';

    // Fillable fields
    protected $fillable = [
        'matiere_id',
        'professeur_id',
        'classe_id',
        'semestre_id',
        'type_fichier',
        'nom_fichier',
        'chemin_fichier'
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
    // FichierPedagogique.php



public function matiere()
{
    return $this->belongsTo(Matiere::class);
}

}

