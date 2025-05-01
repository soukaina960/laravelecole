<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'titre',
        'message',
        'statut',
    ];

    // Relation avec Parent
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}
