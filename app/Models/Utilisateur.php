<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Utilisateur extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'matricule', 'nom', 'email', 'mot_de_passe', 'role',
        'telephone', 'adresse', 'photo_profil',
    ];

    protected $hidden = [
        'mot_de_passe', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }


  
    




    // Relation avec l'étudiant




    public function professeur()
    {
        return $this->hasOne(Professeur::class, 'user_id'); 
    }
    

    public function surveillant()
    {
        return $this->hasOne(Surveillant::class, 'user_id'); 
    }

// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');
}

   
   
    
public function parent()
{
    return $this->hasOne(ParentModel::class, 'user_id'); // user_id = colonne dans la table parents
}

}

