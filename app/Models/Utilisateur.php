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
<<<<<<< HEAD
=======
=======

>>>>>>> 49074a4 (dernier commit)






  
    




>>>>>>> 53e700ca45defad81932aed2dab9a8c96d3f3565
    

   




   




<<<<<<< HEAD
=======

>>>>>>> 49074a4 (dernier commit)
    public function professeur()
    {
        return $this->hasOne(Professeur::class, 'user_id'); 
    }
    
<<<<<<< HEAD

    // Relation avec l'étudiant

=======


    // Relation avec l'étudiant

  

>>>>>>> 53e700ca45defad81932aed2dab9a8c96d3f3565
   



  

  

    

<<<<<<< HEAD
=======

>>>>>>> 53e700ca45defad81932aed2dab9a8c96d3f3565
    public function surveillant()
    {
        return $this->hasOne(Surveillant::class, 'user_id'); 
    }

<<<<<<< HEAD
=======



>>>>>>> 53e700ca45defad81932aed2dab9a8c96d3f3565
// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');}

<<<<<<< HEAD
=======

>>>>>>> 49074a4 (dernier commit)
  public function admin()
{
    return $this->hasOne(Admin::class, 'user_id');
} 
<<<<<<< HEAD
=======

>>>>>>> 49074a4 (dernier commit)
   
   
    


<<<<<<< HEAD

=======







<<<<<<< HEAD
>>>>>>> 85d9dd7 (exman)
>>>>>>> 53e700ca45defad81932aed2dab9a8c96d3f3565
=======


>>>>>>> 49074a4 (dernier commit)
public function parent()
{
    return $this->hasOne(ParentModel::class, 'utilisateur_id');
}

}

