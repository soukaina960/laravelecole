<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'semestre_id',
        'annee_scolaire_id',
        'moyenne_generale',
        'est_traite',
    ];

    // علاقة مع الطالب
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    // علاقة مع الفصل الدراسي
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    // علاقة مع العام الدراسي
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
    public function evaluations()
{
    return $this->hasMany(Evaluation::class);
}

}
