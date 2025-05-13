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

        return $this->belongsTo(Classroom::class, 'classe_id');
    }
    






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