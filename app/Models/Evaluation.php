<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory;

    // Nom de la table associée au modèle
    protected $table = 'evaluations';

    // Les champs autorisés pour les insertions en masse
    protected $fillable = [
        'etudiant_id',
        'professeur_id',
        'annee_scolaire_id',
        'note1',
        'note2',
        'note3',
        'note4',
        'facteur',
        'remarque',
        'note_finale',
        'semestre_id', // Ajout de semestre_id
    ];

    // Définit les relations avec les autres modèles
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    // Calcul de la note finale
    public function getNoteFinaleAttribute()
    {
        $notes = collect([$this->note1, $this->note2, $this->note3, $this->note4])->filter()->all();
        if (count($notes) > 0) {
            return array_sum($notes) / count($notes);
        }
        return null;
    }
    

    // Ajout de la logique pour vérifier ou assigner automatiquement le semestre
    
    
}
