<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorAvailability;

class AvailabilitiesSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        DoctorAvailability::truncate();
        
        foreach($users as $user) {
            for($i=1; $i<=7; $i++) {
                DoctorAvailability::create([
                    'doctor_id' => $user->id,
                    'day_of_week' => $i,
                    'start_time' => '08:00:00',
                    'end_time' => '18:00:00'
                ]);
            }
        }
    }
}
