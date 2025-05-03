<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etudiant;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class EtudiantSeeder extends Seeder
{
    public function run(): void
    {
        $parent_id = 1; // Assure-toi que ce parent existe déjà ou adapte selon l'ID réel
        $classe_id = 1; // Assure-toi aussi que cette classe existe

        for ($i = 1; $i <= 5; $i++) {
            $utilisateur = Utilisateur::create([
                'matricule' => 'E00' . $i,
                'nom' => 'Etudiant' . $i,
                'email' => 'etudiant' . $i . '@example.com',
                'mot_de_passe' => Hash::make('123456'),
                'role' => 'étudiant',
                'telephone' => '06123456' . $i,
                'adresse' => 'Ville ' . $i,
            ]);

            Etudiant::create([
                'utilisateur_id' => $utilisateur->id,
                'nom' => 'Etudiant' . $i,
                'prenom' => 'Prenom' . $i,
                'matricule' => 'E00' . $i,
                'email' => $utilisateur->email,
                'origine' => 'Origine' . $i,
                'parent_id' => $parent_id,
                'date_naissance' => '2005-0' . $i . '-15',
                'sexe' => $i % 2 == 0 ? 'M' : 'F',
                'adresse' => 'Adresse ' . $i,
                'montant_a_payer' => 1500 + ($i * 100),
                'classe_id' => $classe_id,
            ]);
        }
    }
}
