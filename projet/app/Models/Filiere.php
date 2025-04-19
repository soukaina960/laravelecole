<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code'];
    // app/Models/Filiere.php
public function classes()
{
    return $this->hasMany(Classe::class);
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
