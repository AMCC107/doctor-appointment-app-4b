<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorTable extends Component
{
    use WithPagination;

    public function render()
    {
        $defaultSpecialtyId = Specialty::min('id');
        if ($defaultSpecialtyId !== null) {
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

        $doctors = Doctor::with(['user', 'specialty'])->paginate(10);

        return view('livewire.admin.datatables.doctor-table', compact('doctors'));
    }
}
