<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    protected $table = 'fichiers';
    
    protected $fillable = [
        'matiere_id',
        'professeur_id',
        'classe_id',
        'semestre_id',
        'type',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'extension'
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
    
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
    
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }
    // FichierPedagogique.php



}