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
 
    
  
public function evaluations()
{
    return $this->hasMany(Evaluation::class);
}
public function classes()
{
    return $this->belongsToMany(Classe::class, 'professeur_id', 'classe_id');
}
<<<<<<< HEAD
=======
<<<<<<< HEAD

=======
>>>>>>> 537bccd7edc5e547f97ca773e9172f6acb762d1c
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'utilisateur_professeur', 'professeur_id', 'utilisateur_id');
    }
<<<<<<< HEAD

=======
<<<<<<< HEAD

=======
>>>>>>> 537bccd7edc5e547f97ca773e9172f6acb762d1c
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
public function matieres()
{
    return $this->belongsToMany(Matiere::class, 'prof_matiere_classe', 'professeur_id', 'matiere_id')
                ->withPivot('classe_id')
                ->withTimestamps();
}
public function paiementsMensuels($mois = null)
{
    $etudiants = $this->etudiants();

    // Si un mois est spécifié, filtrer par mois
    if ($mois) {
        return $etudiants->join('paiements_mensuels', 'etudiants.id', '=', 'paiements_mensuels.etudiant_id')
                         ->where('paiements_mensuels.mois', $mois)
                         ->get(['etudiants.*', 'paiements_mensuels.*']);
    }

    // Sinon, récupérer tous les paiements mensuels des étudiants
    return $etudiants->join('paiements_mensuels', 'etudiants.id', '=', 'paiements_mensuels.etudiant_id')
                     ->get(['etudiants.*', 'paiements_mensuels.*']);
}


<<<<<<< HEAD

=======
<<<<<<< HEAD
=======
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
public function etudiants()
{
    return $this->belongsToMany(Etudiant::class);
}



<<<<<<< HEAD

=======
>>>>>>> 537bccd7edc5e547f97ca773e9172f6acb762d1c
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
}
