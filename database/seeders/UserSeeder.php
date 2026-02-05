<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que el rol 'Doctor' existe antes de asignarlo
        $doctorRole = Role::firstOrCreate(['name' => 'Doctor']);
        
        $user = User::updateOrCreate(
            ['email' => 'andres17cc95@gmail.com'],
            [
                'name' => 'Andres Castillo',
                'password' => bcrypt('12345678'),
                'id_number' => '1234567890',
                'phone' => '9999302912',
                'address' => 'Calle 123, Colonia 2',
            ]
        );
        
        // Asignar el rol (sincronizar para asegurar que solo tenga este rol)
        $user->syncRoles([$doctorRole->name]);
    }
}
