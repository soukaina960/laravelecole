<?php
// app/Models/Classroom.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';

    protected $fillable = ['name', 'capacite', 'niveau'];

    public function professeur() {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    public function etudiants()
    {
        return $this->hasMany(Etudiant::class, 'class_id');
    }
    public function filiere()
{
    return $this->belongsTo(Filiere::class);
}

}
