<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professeur;

class ProfesseurSeeder extends Seeder
{
    public function run(): void
    {
        Professeur::create([
            'user_id' => 4,
            'nom' => 'Nadia',
            'email' => 'nadia@example.com',
            'specialite' => 'PHP et Laravel',
            'niveau_enseignement' => '1ère année',
            'montant' => 5000,
            'prime' => 500,
            'pourcentage' => 10,
            'total' => 5500,
            'diplome' => 'Master Informatique',
            'date_embauche' => '2020-09-01',
        ]);
    }
}
