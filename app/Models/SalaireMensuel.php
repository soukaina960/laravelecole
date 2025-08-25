<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaireMensuel extends Model
{
    protected $table = 'salaire_professeurs';

    protected $fillable = [
        'professeur_id',
        'mois',
        'annee',
        'total_paiements',
        'salaire',
    ];
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }
}
