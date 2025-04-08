<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','nom','email' ,'specialite', 'niveau_enseignement', 'diplome', 'date_embauche'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id'); 
    }
    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class);
    }
    
  
public function evaluations()
{
    return $this->hasMany(Evaluation::class);
}
public function classes()
{
    return $this->belongsToMany(Classe::class, 'professeur_id', 'classe_id');
}

    
}
