<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementMensuel extends Model
{
    use HasFactory;

    // Définir le nom de la table si nécessaire
    protected $table = 'paiements_mensuels';

    // Les champs qui peuvent être assignés en masse
    protected $fillable = [
        'etudiant_id', 
        'mois', 
        'date_paiement', 
        'est_paye'
    ];

    // Relations avec d'autres modèles (si nécessaire)
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
 
    

    // Si vous souhaitez formater des attributs comme la date
    protected $dates = ['date_paiement'];

    // Si vous voulez que "est_paye" soit un booléen, vous pouvez l'ajouter
    protected $casts = [
        'est_paye' => 'boolean',
    ];
    protected static function booted()
{
    static::saved(function ($paiement) {
        $paiement->etudiant->professeurs->each->recalculerSalaire();
    });

    static::deleted(function ($paiement) {
        $paiement->etudiant->professeurs->each->recalculerSalaire();
    });
}
}
