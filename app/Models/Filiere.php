<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code'];
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
    
public function filiere()
{
    return $this->belongsTo(Filiere::class, 'filiere_id');
}

public function etudiants()
{
    return $this->hasMany(Etudiant::class, 'classe_id');
}
}
