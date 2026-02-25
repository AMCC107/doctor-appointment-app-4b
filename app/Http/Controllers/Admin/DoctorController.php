<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
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
}
