<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'description',
        'date',
        'professeur_id',
        'class_id',
        'matiere_id',
        'surveillant_id',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
    public function matiere()
    {
        return $this->belongsTo(Matiere::class , 'matiere_id');
    }
    public function surveillant()
    {
        return $this->belongsTo(Surveillant::class , 'surveillant_id');
    }
}

