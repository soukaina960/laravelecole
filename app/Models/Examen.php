<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $fillable = [
        'classe_id',
        'matiere_id',
        'professeur_id',
        'date',
        'jour',
        'heure_debut',
        'heure_fin'
    ];

   public function classroom()
{
    return $this->belongsTo(Classroom::class, 'classe_id');
}


    public function matiere() {
        return $this->belongsTo(Matiere::class);
    }

    public function professeur() {
        return $this->belongsTo(Professeur::class);
    }
}
