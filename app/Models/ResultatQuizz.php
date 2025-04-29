<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultatQuizz extends Model
{
    use HasFactory;

    protected $table = 'resultats_quizz';

    protected $fillable = [
        'quizz_id',
        'etudiant_id',
        'note',
        'date_soumission'
    ];

    public function quizz()
    {
        return $this->belongsTo(Quizz::class, 'quizz_id');
    }

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'etudiant_id');
    }
}