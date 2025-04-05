<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'type',
        'description',
        'date',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}

