<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $fillable = [
        'nom',
        'filiere_id'
    ];
    
    // Define relationship with Filiere
    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }
    public function etudiants()
{
    return $this->hasMany(Etudiant::class, 'classe_id');
}
public function attendances()
{
    return $this->hasMany(Attendance::class);
}
// app/Models/Classe.php

}