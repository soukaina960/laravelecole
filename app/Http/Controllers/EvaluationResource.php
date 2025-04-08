<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'etudiant_id' => $this->etudiant_id,
            'etudiant_nom' => $this->etudiant->nom,
            'professeur_id' => $this->professeur_id,
            'matiere_id' => $this->matiere_id,
            'annee_scolaire_id' => $this->annee_scolaire_id,
            'note1' => $this->note1,
            'note2' => $this->note2,
            'note3' => $this->note3,
            'note4' => $this->note4,
            'facteur' => $this->facteur,
            'note_finale' => $this->note_finale,
            'remarque' => $this->remarque,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}