<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// IMPORTANTE: Importamos el modelo para poder usarlo
use App\Models\Specialty;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'Cardiología',
            'Pediatría',
            'Dermatología',
            'Neurología',
            'Cirugía General',
            'Psiquiatría',
            'Traumatología',
            'Medicina Interna',
            'Ginecología',
            'Oftalmología',
            'Ortopedia',
            'Veterinaria',
        ];

        foreach ($specialties as $name) {
            Specialty::firstOrCreate(['name' => $name]);
        }
    }
}