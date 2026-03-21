<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('patient.user', fn ($q) => $q->where('name', 'like', "%{$term}%"))
                ->orWhereHas('doctor.user', fn ($q) => $q->where('name', 'like', "%{$term}%"));
        }

        $appointments = $query->paginate($request->integer('per_page', 10))->withQueryString();
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string'
        ]);

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => 15,
            'reason' => $request->reason,
            'status' => 1,
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Cita programada con éxito.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => [
                'required',
                'date',
                Rule::when($request->date !== $appointment->date->format('Y-m-d'), 'after_or_equal:today'),
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string',
        ]);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function consultation(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        return view('admin.appointments.consultation', compact('appointment'));
    }
}
