<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Services\AppointmentWhatsAppNotifier;

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
            'start_hour' => 'required|integer|between:0,23',
            'start_minute' => 'required|integer|in:0,15,30,45',
            'end_hour' => 'required|integer|between:0,23',
            'end_minute' => 'required|integer|in:0,15,30,45',
            'reason' => 'nullable|string',
        ]);

        $startTime = sprintf('%02d:%02d', (int) $request->start_hour, (int) $request->start_minute);
        $endTime = sprintf('%02d:%02d', (int) $request->end_hour, (int) $request->end_minute);
        $startM = (int) $request->start_hour * 60 + (int) $request->start_minute;
        $endM = (int) $request->end_hour * 60 + (int) $request->end_minute;
        if ($endM <= $startM) {
            return back()->withErrors(['end_time' => 'La hora fin debe ser posterior a la hora de inicio.'])->withInput();
        }

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => 15,
            'reason' => $request->reason,
            'status' => 1,
        ]);

        $fresh = $appointment->fresh(['patient.user', 'doctor.user']);
        $whatsapp = $fresh instanceof Appointment
            ? app(AppointmentWhatsAppNotifier::class)->notifyAppointmentCreated($fresh)
            : ['sent' => false, 'reason' => 'exception', 'detail' => 'No se pudo recargar la cita tras guardarla.'];

        $redirect = redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Cita programada con éxito.');

        if (! ($whatsapp['sent'] ?? false)) {
            $warning = match ($whatsapp['reason'] ?? '') {
                'no_phone' => 'WhatsApp no enviado: el paciente no tiene teléfono en su usuario ni en contacto de emergencia. Complétalo y vuelve a intentar o envía manualmente.',
                'twilio_failed' => 'La cita se guardó, pero rechazó WhatsApp: '.($whatsapp['detail'] ?? 'sin detalle').'.',
                'exception' => 'La cita se guardó, pero falló el envío de WhatsApp: '.($whatsapp['detail'] ?? 'error desconocido').'. Revisa laravel.log.',
                default => 'No se pudo verificar el envío de WhatsApp. Si no llegó el mensaje, revisa el teléfono del paciente y laravel.log.',
            };
            $redirect->with('warning', $warning);
        } else {
            $redirect->with('info', 'Se envió la confirmación por WhatsApp al paciente.');
        }

        return $redirect;
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
            'start_hour' => 'required|integer|between:0,23',
            'start_minute' => 'required|integer|in:0,15,30,45',
            'end_hour' => 'required|integer|between:0,23',
            'end_minute' => 'required|integer|in:0,15,30,45',
            'reason' => 'nullable|string',
        ]);

        $startTime = sprintf('%02d:%02d', (int) $request->start_hour, (int) $request->start_minute);
        $endTime = sprintf('%02d:%02d', (int) $request->end_hour, (int) $request->end_minute);
        $startM = (int) $request->start_hour * 60 + (int) $request->start_minute;
        $endM = (int) $request->end_hour * 60 + (int) $request->end_minute;
        if ($endM <= $startM) {
            return back()->withErrors(['end_time' => 'La hora fin debe ser posterior a la hora de inicio.'])->withInput();
        }

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $startTime,
            'end_time' => $endTime,
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
