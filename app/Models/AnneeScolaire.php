<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    use HasFactory;

    // Spécifie la table associée au modèle
    protected $table = 'annees_scolaires';

    // La liste des colonnes mass assignables
    protected $fillable = [
        'annee', // L'année scolaire (par exemple : "2023-2024")
    ];
    public function semestres() {
        return $this->hasMany(Semestre::class);
    }
    
}
