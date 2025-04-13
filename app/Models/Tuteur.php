<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuteur extends Model
{
    // Spécifie explicitement le nom de la table
    protected $table = 'parents';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'profession'
    ];

    // Relation avec les étudiants
    public function etudiants()
    {
        return $this->hasMany(Etudiant::class, 'parent_id');
    }
}