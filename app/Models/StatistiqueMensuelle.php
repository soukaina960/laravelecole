<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistiqueMensuelle extends Model
{
    protected $table = 'statistiques_mensuelles';

    protected $fillable = [
        'mois',
        'annee',
        'etudiants',
        'professeurs',
        'classes',
        'revenus',
        'depenses',
        'salaires',
        'charges',
        'alertes',
        'reste',
    ];
}
