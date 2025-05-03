<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Surveillant;

class SurveillantSeeder extends Seeder
{
    public function run(): void
    {
        Surveillant::create([
            'user_id' => 3,
            'nom' => 'Karim',
            'prenom' => 'El Idrissi',
            'email' => 'karim@example.com',
            'password' => \Hash::make('survpass')
        ]);
    }
}
