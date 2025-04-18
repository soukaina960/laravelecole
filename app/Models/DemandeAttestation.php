<?php
// app/Models/DemandeAttestation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeAttestation extends Model
{
    use HasFactory;

    protected $fillable = ['etudiant_id', 'traitee', 'lien_attestation'];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}

