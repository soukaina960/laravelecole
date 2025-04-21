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

=======
<<<<<<< HEAD
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7

  
    

<<<<<<< HEAD


    // Relation avec l'étudiant

=======
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
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
    public function professeur()
    {
        return $this->hasOne(Professeur::class, 'user_id'); 
    }
    
<<<<<<< HEAD
    public function surveillant()
    {
        return $this->hasOne(Surveillant::class, 'user_id'); 
    }
=======
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');}

   
   
    
<<<<<<< HEAD

=======
>>>>>>> 537bccd7edc5e547f97ca773e9172f6acb762d1c
>>>>>>> 32f397de9c28bc07174e4af731be108786415da7
public function parent()
{
    return $this->hasOne(ParentModel::class, 'utilisateur_id');
}

}

