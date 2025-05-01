<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quizz_id',
        'type',
        'texte_question',
        'points'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quizz_id');
    }

    public function reponsesPossibles()
    {
        return $this->hasMany(ReponsePossible::class);
    }

    public function reponsesEleves()
    {
        return $this->hasMany(ReponseEleve::class);
    }
}