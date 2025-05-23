<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\classroom;
use App\Models\PaiementMensuel;




class Etudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'prenom',
        'matricule',
        'email',
        'origine',
        'parent_id',
        'date_naissance',
        'sexe',
        'adresse',
        'photo_profil',
        'montant_a_payer',
        'classe_id',
    ];


    public function filiere()
{

    return $this->belongsTo(Filiere::class);
}
public function attendances()
{
    return $this->hasMany(Attendance::class);
}


    public function parent()
    {
        return $this->belongsTo(ParentModel::class);
    }
    public function parentModel()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classe_id');
    }

    public function montantAPayer()
    {
        return $this->montant_a_payer ?? $this->classe->montant;
    }
    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class, 'etudiant_professeur', 'etudiant_id', 'professeur_id');
    }
    // App\Models\Etudiant.php

    public function paiements()
    {
        return $this->hasMany(PaiementMensuel::class, 'etudiant_id');
    }
    

    
    

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function retards()
    {
        return $this->hasMany(Retard::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function emailParent()
{
    return $this->hasOne(EmailParent::class);
}














<<<<<<< HEAD
















=======
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11


































// Etudiant.php (modèle)
public function utilisateur()
{
    return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
}




<<<<<<< HEAD
    

public function professeur() {
    return $this->belongsTo(Professeur::class);
}
























  

    

























=======
>>>>>>> 9b7d10f01a260c9625961aad17ed4e1345f6cd11
public function notes()
{
    return $this->hasMany(NoteMatiere::class);
}
// app/Models/Etudiant.php

public function evaluations()
{
    return $this->hasMany(Evaluation::class);
}




public function paiements_mensuels()
{
    return $this->hasMany(PaiementMensuel::class);
}
protected static function booted()
{
    static::deleting(function ($etudiant) {
        // Pour chaque professeur lié à l'étudiant
        $etudiant->professeurs->each(function ($professeur) {
            $professeur->recalculerSalaire(); // Met à jour automatiquement
        });
    });
}


public function paiementsMensuels()
{
    return $this->hasMany(PaiementMensuel::class);
}


}