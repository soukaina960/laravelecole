<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surveillant extends Model
{
    use HasFactory;

    protected $table = 'utilisateurs'; // Parce que les surveillants f table utilisateurs

    protected $fillable = [
        'nom',
        'email',
        'matricule',
        'mot_de_passe',
        'role',
        'telephone',
        'adresse',
        'photo_profil',
    ];

    // 🔁 Relations

    public function absences()
    {
        return $this->hasMany(Absence::class, 'surveillant_id');
    }
  public function utilisateur()
    {
        return $this->hasOne(Utilisateur::class, 'id', 'user_id'); // Si la relation se fait via `user_id`
    }

    public function retards()
    {
        return $this->hasMany(Retard::class, 'surveillant_id');
    }

    public function emploiSurveillant()
    {
        return $this->hasMany(EmploiSurveillant::class, 'surveillant_id');
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class, 'surveillant_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'surveillant_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'surveillant_id');
    }
}
