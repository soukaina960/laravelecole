<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperSurveillant extends Model
{
    use HasFactory;

    protected $table = 'utilisateurs'; // même table que surveillant

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'matricule',
        'role',
        'telephone',
        'adresse',
        'photo_profil',
    ];

    public function surveillants()
    {
        return $this->hasMany(Surveillant::class, 'super_surveillant_id');
    }

    public function emplois()
    {
        return $this->hasMany(EmploiSurveillant::class, 'super_surveillant_id');
    }
}
