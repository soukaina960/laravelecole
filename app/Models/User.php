<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom_complet', 'email', 'mot_de_passe', 'telephone', 'adresse', 'photo_profil', 'role', 'statut_compte',
    ];

    protected $hidden = ['mot_de_passe'];

    // Relation avec le professeur
    public function professeur() {
        return $this->hasOne(Professeur::class);
    }

    // Relation avec l'étudiant
    public function etudiant() {
        return $this->hasOne(Etudiant::class);
    }
    protected $table = 'utilisateurs';
}


