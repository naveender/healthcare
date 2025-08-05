<?php

namespace Database\Seeders;

use App\Models\HealthcareProfessional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HealthcareProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          HealthcareProfessional::insert([
        ['name' => 'Dr. A Sharma', 'specialty' => 'Cardiologist'],
        ['name' => 'Dr. B Mehta', 'specialty' => 'Dermatologist'],
        ['name' => 'Dr. C Rao', 'specialty' => 'Neurologist'],
    ]);
    }
}
