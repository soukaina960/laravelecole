<?php
namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';  // Assurez-vous que la table est bien 'classrooms'

    protected $fillable = ['name', 'capacite', 'niveau',  'filiere_id'];

    // Relation avec Professeur
    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'professeur_id');  // Assurez-vous que 'professeur_id' existe dans la table classrooms
    }

    // Relation avec Etudiant
    public function etudiants()
    {

        return $this->hasMany(Etudiant::class, 'class_id'); // Utilise 'classe_id' qui est la clé étrangère dans la table etudiants
    }
    
    
    public function absences()
    {
        return $this->hasMany(Absence::class, 'class_id');
    }
    // Relation avec Filiere
    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');  // Assurez-vous que 'filiere_id' existe dans la table classrooms

        return $this->hasMany(Etudiant::class, 'classe_id');  // Assurez-vous que la clé étrangère est 'classe_id'
    }




    public function evenements()
    {
        return $this->hasMany(Evenement::class);
    }
    
    // Relation avec Filiere
 


    // Relation avec Filiere
 



    // Relation avec Filiere
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }


<<<<<<< HEAD


    

=======
    
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
}
