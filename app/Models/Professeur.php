<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialite', 'niveau_enseignement', 'diplome', 'date_embauche'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id'); 
    }
    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class);
    }

    
}
