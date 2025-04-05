<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retard extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'date',
        'heure',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}
