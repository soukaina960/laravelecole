<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReponsePossible extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'texte',
        'est_correcte'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}