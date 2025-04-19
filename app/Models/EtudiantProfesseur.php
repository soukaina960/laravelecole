<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EtudiantProfesseur extends Model
{
    use HasFactory;

    // Nom de la table associée au modèle
    protected $table = 'etudiant_professeur';

    // Les attributs qui sont mass-assignables
    protected $fillable = [
        'etudiant_id',
        'professeur_id',
    ];

    // Définition de la relation avec le modèle Etudiant
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    // Définition de la relation avec le modèle Professeur
    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'professeur_id');
    }
}
