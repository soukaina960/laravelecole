<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReponseEleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'etudiant_id',
        'texte_reponse',
        'reponse_choisie_id',
        'fichier_joint'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'etudiant_id');
    }

    public function reponseChoisie()
    {
        return $this->belongsTo(ReponsePossible::class, 'reponse_choisie_id');
    }
}