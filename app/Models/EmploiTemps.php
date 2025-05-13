<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploiTemps extends Model
{
    protected $table = 'emplois_temps';
    
    protected $fillable = [
        'classe_id',
        'jour',
        'creneau_id',
        'matiere_id',
        'professeur_id',
        'salle' // Champ texte, pas une relation
    ];

    // Relations
    public function classe()
    {
<<<<<<< HEAD

=======
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
        return $this->belongsTo(Classroom::class, 'classe_id');
    }
    

<<<<<<< HEAD





=======
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'professeur_id');
    }

    public function creneau()
    {
        return $this->belongsTo(Creneau::class, 'creneau_id');
    }
}