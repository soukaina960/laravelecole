<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etudiant;
use App\Models\ParentModel;

class ParentEtEtudiantSeeder extends Seeder
{
    public function run(): void
    {
        $parent = ParentModel::create([
            'nom' => 'Elhaj',
            'prenom' => 'Mohamed',
            'email' => 'faroohabyd@gmail.com',
            'telephone' => '0612345678',
            'adresse' => 'Agadir',
            'profession' => 'Comptable',
            'user_id' => 2,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Etudiant::create([
                'utilisateur_id' => 1,
                'nom' => 'Farah',
                'prenom' => 'Aitouhlal',
                'matricule' => 'E00' . $i,
                'email' => 'farah'.$i.'@example.com',
                'origine' => 'Rabat',
                'parent_id' => $parent->id,
                'date_naissance' => '2004-05-10',
                'sexe' => 'F',
                'adresse' => 'Rabat Centre',
                'montant_a_payer' => 1500,
                'classe_id' => 1,
            ]);
        }
    }
}
