<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploiSurveillance extends Model
{
    use HasFactory;

    protected $fillable = [
        'jour',
        'heure_debut',
        'heure_fin',
        'surveillant_id',
    ];

    public function surveillant()
    {
        return $this->belongsTo(Utilisateur::class, 'surveillant_id');
    }
}
