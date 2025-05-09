<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surveillant extends Model
{
    use HasFactory;

    protected $table = 'surveillant'; // Parce que les surveillants f table utilisateurs

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
    ];
    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id'); 
    }
    public function absences()
    {
        return $this->hasMany(Absence::class , 'surveillant_id');
    }
    public function retards()
    {
        return $this->hasMany(Retard::class , 'surveillant_id');
    }
    public function incidents()
    {
        return $this->hasMany(Incident::class , 'surveillant_id');
    }
   



  
}
