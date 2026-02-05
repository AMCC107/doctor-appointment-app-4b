<?php

namespace Database\Seeders;

use App\Models\BloodType;
use Illuminate\Database\Seeder;

class BloodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodTypes = [
            'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
        ];

        foreach ($bloodTypes as $bloodType) {
            // Se usa 'type' porque así lo definiste en la migración
            BloodType::firstOrCreate([
                'type' => $bloodType
            ]);
        }

        $this->command->info('Tipos de sangre cargados correctamente.');
    }
}