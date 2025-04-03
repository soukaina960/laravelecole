// app/Models/ParentModel.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents'; // Spécifie explicitement le nom de table

    protected $fillable = [
        'nom',
        'prenom', 
        'email',
        'telephone',
        'adresse',
        'profession'
    ];

    public function etudiants()
    {
        return $this->hasMany(Etudiant::class, 'parent_id');
    }
}