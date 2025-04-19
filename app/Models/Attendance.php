<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// app/Models/Attendance.php
class Attendance extends Model
{
    protected $fillable = ['classe_id', 'etudiant_id', 'date', 'status'];
    
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
    
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}
