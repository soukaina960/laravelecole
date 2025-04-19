<?php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = ['name', 'classroom_id'];

    // Relation avec la classe Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
