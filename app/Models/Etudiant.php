<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    'prenom'
    ,'matricule', 'email', 'date_naissance', 'sexe', 
        'adresse', 'photo_profil', 'montant_a_payer',
        'parent_id', 'origine','filiere_id',
    'classe_id',
    'category',
    ];
    public function filiere()
{
    return $this->belongsTo(Filiere::class);
}
public function attendances()
{
    return $this->hasMany(Attendance::class);
}

public function classe()
{
    return $this->belongsTo(Classe::class, 'classe_id');
}
    public function parent()
    {
        return $this->belongsTo(Parent::class);
    }


    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
    public function montantAPayer()
    {
        return $this->montant_a_payer ?? $this->classe->montant;
    }
    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class);
    }


}