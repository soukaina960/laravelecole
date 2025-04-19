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

    public function professeur()
    {
        return $this->hasOne(Professeur::class);
    }
    
// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');
}

public function professeurs()
{
    return $this->belongsToMany(Professeur::class, 'utilisateur_professeur', 'utilisateur_id', 'professeur_id');
}
    protected $table = 'utilisateurs';
}


