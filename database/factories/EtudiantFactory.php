<?php

namespace Database\Factories;

use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EtudiantFactory extends Factory
{
    protected $model = Etudiant::class;

    public function definition()
    {
        return [
            'utilisateur_id' => $this->faker->unique()->randomNumber(),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'matricule' => Str::random(10),
            'email' => $this->faker->unique()->safeEmail,
            'origine' => $this->faker->country,
            'parent_id' => \App\Models\Parent::factory(), // Assure-toi que tu as une factory pour Parent
            'date_naissance' => $this->faker->date(),
            'sexe' => $this->faker->randomElement(['Homme', 'Femme']),
            'adresse' => $this->faker->address,
            'photo_profil' => $this->faker->imageUrl(),
            'montant_a_payer' => $this->faker->randomFloat(2, 100, 500),
            'classe_id' => \App\Models\Classe::factory(), // Assure-toi que tu as une factory pour Classe
        ];
    }
}
