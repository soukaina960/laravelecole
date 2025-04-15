<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'date',
        'motif',
        'justifiee',
        'professeur_id' // Ajout de professeur_id
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }
}
