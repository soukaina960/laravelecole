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
=======



>>>>>>> d117421 (acceuil)
  
    



<<<<<<< HEAD
    

   




   




>>>>>>> d29b252f9cbb27f9e29cc0ab9b7671adf77c01da
    public function professeur()
    {
        return $this->hasOne(Professeur::class, 'user_id'); 
    }
    
=======

    // Relation avec l'étudiant

    public function professeur() 
    {
        return $this->hasOne(Professeur::class);
    }

   



  

  

    

>>>>>>> d117421 (acceuil)
    public function surveillant()
    {
        return $this->hasOne(Surveillant::class, 'user_id'); 
    }
<<<<<<< HEAD
=======

>>>>>>> d117421 (acceuil)
// Utilisateur.php (modèle)
public function etudiant()
{
    return $this->hasOne(Etudiant::class, 'utilisateur_id');}

<<<<<<< HEAD
  public function admin()
{
    return $this->hasOne(Admin::class, 'user_id');
} 
=======
   
   
    


<<<<<<< HEAD
=======




>>>>>>> d117421 (acceuil)
>>>>>>> d29b252f9cbb27f9e29cc0ab9b7671adf77c01da
public function parent()
{
    return $this->hasOne(ParentModel::class, 'utilisateur_id');
}

}

