<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code', 'description'];
    
    
    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'filiere_id');  // Assurez-vous que la relation est correctement définie
    }
public function etudiants()
{
    return $this->hasMany(Etudiant::class, 'classe_id');
}
}
