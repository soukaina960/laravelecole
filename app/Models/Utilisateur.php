<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory;

    protected $table = 'utilisateurs';

   // app/Models/Utilisateur.php

protected $fillable = [
    'matricule', // Ajouter ce champ
    'nom',
    'email',
    'mot_de_passe',
    'role',
    'telephone',
    'adresse',
    'photo_profil'
];

    protected $hidden = ['mot_de_passe'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['mot_de_passe'] = bcrypt($value);
    }

public function etudiant() {
    return $this->hasOne(Etudiant::class);
}
public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
