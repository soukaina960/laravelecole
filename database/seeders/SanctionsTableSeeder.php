<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SanctionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sanctions')->insert([
            [
                'type_sanction' => 'Avertissement verbal',
                'description' => 'Première alerte après des absences répétées',
                'nombre_absences_min' => 3,
                'niveau_concerne' => 'primaire, collège',
            ],
            [
                'type_sanction' => 'Avertissement écrit',
                'description' => 'Lettre adressée aux parents',
                'nombre_absences_min' => 5,
                'niveau_concerne' => 'collège, lycée',
            ],
            [
                'type_sanction' => 'Rencontre avec parents',
                'description' => 'RDV obligatoire avec les parents',
                'nombre_absences_min' => 7,
                'niveau_concerne' => 'collège, lycée',
            ],
            [
                'type_sanction' => 'Retenue',
                'description' => 'L\'élève doit rester après les cours',
                'nombre_absences_min' => 8,
                'niveau_concerne' => 'collège, lycée',
            ],
            [
                'type_sanction' => 'Exclusion temporaire',
                'description' => 'Exclusion de 1 à 3 jours',
                'nombre_absences_min' => 10,
                'niveau_concerne' => 'lycée',
            ],
            [
                'type_sanction' => 'Exclusion définitive',
                'description' => 'Après plusieurs avertissements',
                'nombre_absences_min' => 15,
                'niveau_concerne' => 'lycée',
            ],
        ]);
    }
}
