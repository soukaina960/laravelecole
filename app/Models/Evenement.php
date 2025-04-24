<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description', 'date_debut', 'date_fin', 'lieu', 'class_id'];


    public function classe()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
    
}

