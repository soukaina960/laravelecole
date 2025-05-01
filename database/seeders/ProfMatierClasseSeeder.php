<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfMatiereClasse;

class ProfMatiereClasseSeeder extends Seeder
{
    public function run(): void
    {
        ProfMatiereClasse::create([
            'professeur_id' => 1,
            'matiere_id' => 2,
            'classe_id' => 1
        ]);
    }
}
