<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = ['nom', 'annee_scolaire_id'];

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
