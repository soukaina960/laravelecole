<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'description',
        'date',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}

