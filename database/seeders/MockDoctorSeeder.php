<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorAvailability;
use Illuminate\Support\Facades\Hash;

class MockDoctorSeeder extends Seeder
{
    public function run()
    {
        $user2 = User::firstOrCreate(
            ['email' => 'carlos@demo.com'],
            ['name' => 'Dr. Carlos Perez', 'password' => Hash::make('password')]
        );
        $user3 = User::firstOrCreate(
            ['email' => 'luis@demo.com'],
            ['name' => 'Dr. Luis Torres', 'password' => Hash::make('password')]
        );

        DoctorAvailability::where('doctor_id', $user2->id)->delete();
        DoctorAvailability::where('doctor_id', $user3->id)->delete();

        for($i=1; $i<=7; $i++) {
            DoctorAvailability::create([
                'doctor_id' => $user2->id,
                'day_of_week' => $i,
                'start_time' => '08:00:00',
                'end_time' => '18:00:00'
            ]);
            DoctorAvailability::create([
                'doctor_id' => $user3->id,
                'day_of_week' => $i,
                'start_time' => '08:00:00',
                'end_time' => '18:00:00'
            ]);
        }
    }
}
