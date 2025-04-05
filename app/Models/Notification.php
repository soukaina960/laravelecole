<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'contenu',
        'envoyee_par',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function envoyeur()
    {
        return $this->belongsTo(Utilisateur::class, 'envoyee_par');
    }
}
