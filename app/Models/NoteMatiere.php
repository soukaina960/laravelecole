<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteMatiere extends Model
{
    use HasFactory;

    protected $table = 'note_matiere';  // Indiquer explicitement le nom de la table

    protected $fillable = [
        'etudiant_id',
        'professeur_id',
        'note_finale',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }
}
