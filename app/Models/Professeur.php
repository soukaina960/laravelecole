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














public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'utilisateur_professeur', 'professeur_id', 'utilisateur_id');
    }














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















public function etudiants()
{
    return $this->belongsToMany(Etudiant::class);
}
public function recalculerSalaire()
{
    // Somme des montants payés par les étudiants de ce prof
    $totalMontants = $this->etudiants()
        ->whereHas('paiements_mensuels', function ($query) {
            $query->where('est_paye', true);
        })
        ->withSum(['paiements_mensuels as montant_paye' => function ($query) {
            $query->where('est_paye', true);
        }], 'montant')
        ->get()
        ->sum('montant_paye');










<<<<<<< HEAD

=======
>>>>>>> e8fd732 (Normalize)
    // Calcul du nouveau salaire
    $this->total = ($this->pourcentage / 100) * $totalMontants + $this->prime;
    $this->save();
}
protected static function booted()
{
    static::updated(function ($professeur) {
        if ($professeur->isDirty(['pourcentage', 'prime'])) {
            $professeur->recalculerSalaire();
        }
    });
}









<<<<<<< HEAD
=======

>>>>>>> e8fd732 (Normalize)
}
