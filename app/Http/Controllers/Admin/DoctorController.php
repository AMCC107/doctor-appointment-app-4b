<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return view('admin.doctors.index');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load(['user', 'specialty']);
        $specialties = Specialty::orderBy('name')->get();

        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'medical_license' => 'required|string|max:25',
            'bio' => 'nullable|string',
        ], [
            'specialty_id.required' => 'Debe seleccionar una especialidad.',
            'specialty_id.exists' => 'La especialidad seleccionada no es válida.',
            'medical_license.required' => 'La licencia médica es obligatoria.',
            'medical_license.max' => 'La licencia médica no puede tener más de 25 caracteres.',
            'medical_license.string' => 'La licencia médica debe ser texto.',
            'bio.string' => 'La biografía debe ser texto.',
        ]);

        $doctor->update($validated);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor actualizado',
            'text' => 'El doctor se actualizó correctamente',
        ]);

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Display the schedules grid for a specific doctor (mismo diseño que gestor de calendario).
     */
    public function schedules(Doctor $doctor)
    {
        $doctorUserId = $doctor->user_id;
        $availabilities = DoctorAvailability::where('doctor_id', $doctorUserId)->get();
        $days = $this->getDays();

        return view('admin.doctors.schedules', compact('doctor', 'availabilities', 'days'));
    }

    /**
     * Save schedule: acepta availabilities[] (day_of_week, start_time, end_time) como el gestor de calendario.
     */
    public function storeSchedules(Request $request, Doctor $doctor)
    {
        $request->validate([
            'availabilities' => 'array',
            'availabilities.*.day_of_week' => 'required|integer|min:1|max:7',
            'availabilities.*.start_time' => 'required|string',
            'availabilities.*.end_time' => 'required|string',
        ]);

        $doctorUserId = $doctor->user_id;
        DoctorAvailability::where('doctor_id', $doctorUserId)->delete();

        foreach ($request->input('availabilities', []) as $avail) {
            DoctorAvailability::create([
                'doctor_id' => $doctorUserId,
                'day_of_week' => (int) $avail['day_of_week'],
                'start_time' => Carbon::parse($avail['start_time'])->format('H:i:s'),
                'end_time' => Carbon::parse($avail['end_time'])->format('H:i:s'),
            ]);
        }

        return redirect()->route('admin.doctors.schedules', $doctor)->with('success', 'Horarios guardados correctamente.');
    }

    private function getDays(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }
}
