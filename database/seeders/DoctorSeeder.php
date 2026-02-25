<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSpecialtyId = Specialty::min('id');
        if ($defaultSpecialtyId === null) {
            return;
        }

        foreach (User::role('Doctor')->get() as $user) {
            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $defaultSpecialtyId,
                    'medical_license' => null,
                    'bio' => null,
                ]
            );
        }
    }
}
