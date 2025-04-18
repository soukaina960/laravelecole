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
        return $this->hasMany(Etudiant::class, 'classe_id');  // Assurez-vous que la clé étrangère est 'classe_id'
    }

    // Relation avec Filiere
    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }
    
}
