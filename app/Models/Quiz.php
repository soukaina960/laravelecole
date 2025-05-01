<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $table = 'quiz';
    // Indiquer les champs qui peuvent être remplis en masse
    protected $fillable = [
        'class_id',
        'matiere_id',
        'question_text',
        'answer',
        'description',
    ];

    /**
     * Récupérer la classe associée au quiz
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    /**
     * Récupérer la matière associée au quiz
     */
    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
