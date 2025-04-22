<?php

// app/Models/Creneau.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Creneau extends Model
{
    protected $table = 'creneaux';
    protected $fillable = ['heure_debut', 'heure_fin'];

    public function emplois()
    {
        return $this->hasMany(EmploiTemps::class);
    }
}
