<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        $this->call([
            UtilisateurSeeder::class,
            ProfesseurSeeder::class,
            SurveillantSeeder::class,
            ParentEtEtudiantSeeder::class,
            ProfMatiereClasseSeeder::class,
            EtudiantSeeder::class
        ]);
    }
    
}
// SanctionsTableSeeder::class,