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
<<<<<<< HEAD

  
    

    public function professeur() 
    {
        return $this->hasOne(Professeur::class);
    }

    // Relation avec l'étudiant
    public function etudiant() 
    {
        return $this->hasOne(Etudiant::class);
    }


=======
    public function professeur()
    {
        return $this->hasOne(Professeur::class, 'user_id'); 
    }
    
// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');}

   
   
    
>>>>>>> 537bccd7edc5e547f97ca773e9172f6acb762d1c
public function parent()
{
    return $this->hasOne(ParentModel::class, 'utilisateur_id');
}

}

