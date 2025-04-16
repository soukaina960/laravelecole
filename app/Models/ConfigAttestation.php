<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigAttestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_ecole', 
        'nom_faculte', 
        'annee_scolaire', 
        'telephone', 
        'fax', 
        'logo_path'
    ];
}
