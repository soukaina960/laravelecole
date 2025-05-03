<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        Utilisateur::insert([
            [
                'id' => 1,
                'matricule' => 'U001',
                'nom' => 'Farah',
                'email' => 'farah@example.com',
                'mot_de_passe' => Hash::make('123456'),
                'role' => 'étudiant',
                'telephone' => '0600000001',
                'adresse' => 'Rabat',
            ],
            [
                'id' => 2,
                'matricule' => 'U002',
                'nom' => 'Yassine',
                'email' => 'yassine@example.com',
                'mot_de_passe' => Hash::make('parentpass'),
                'role' => 'parent',
                'telephone' => '0600000002',
                'adresse' => 'Casablanca',
            ],
            [
                'id' => 3,
                'matricule' => 'U003',
                'nom' => 'Mr. Karim',
                'email' => 'karim@example.com',
                'mot_de_passe' => Hash::make('survpass'),
                'role' => 'surveillant',
                'telephone' => '0600000003',
                'adresse' => 'Marrakech',
            ],
            [
                'id' => 4,
                'matricule' => 'U004',
                'nom' => 'Mme. Nadia',
                'email' => 'nadia@example.com',
                'mot_de_passe' => Hash::make('123456'),
                'role' => 'professeur',
                'telephone' => '0600000004',
                'adresse' => 'Fès',
            ],
        ]);
    }
}
