<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DoctorAvailability;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Specialty;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])->orderBy('appointment_date', 'desc')->paginate(10);
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $specialties = Specialty::all();
        $doctors = User::all(); // keeping just in case, but unused for the dynamic search
        return view('appointments.create', compact('specialties', 'doctors'));
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'specialty_id' => 'nullable|exists:specialties,id',
        ]);

        $date = $request->date;
        $start_time = Carbon::parse($request->time)->format('H:i:s');
        $end_time = Carbon::parse($request->time)->addMinutes(30)->format('H:i:s');
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        // Base query for users who are doctors. 
        // We will assume "doctor" is any User who has a record in doctors table.
        $doctorsQuery = \App\Models\Doctor::with('user', 'specialty');
        
        if ($request->filled('specialty_id') && $request->specialty_id !== 'Todos') {
            $doctorsQuery->where('specialty_id', $request->specialty_id);
        }

        $eligibleDoctors = $doctorsQuery->get();

        $availableSlots = [];

        foreach ($eligibleDoctors as $doctorRecord) {
            $doctorUserId = $doctorRecord->user_id;

            // 1. Check Master Availability
            $hasMaster = DoctorAvailability::where('doctor_id', $doctorUserId)
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $start_time)
                ->where('end_time', '>=', $end_time)
                ->exists();

            if (!$hasMaster) {
                continue;
            }

            // 2. Check Conflicts
            $hasConflict = Appointment::where('doctor_id', $doctorUserId)
                ->where('appointment_date', $date)
                ->where('status', 'Programado')
                ->where(function ($query) use ($start_time, $end_time) {
                    $query->where('start_time', '<', $end_time)
                          ->where('end_time', '>', $start_time);
                })->exists();

            if (!$hasConflict) {
                $availableSlots[] = [
                    'doctor_id' => $doctorUserId,
                    'doctor_name' => $doctorRecord->user->name ?? 'Doctor',
                    'specialty' => $doctorRecord->specialty->name ?? 'Sin Especialidad',
                    'start_time' => Carbon::parse($start_time)->format('H:i'),
                    'end_time' => Carbon::parse($end_time)->format('H:i'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'slots' => $availableSlots
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $doctor_id = $request->doctor_id;
        $date = $request->appointment_date;
        $start_time = Carbon::parse($request->start_time)->format('H:i:s');
        $end_time = Carbon::parse($request->end_time)->format('H:i:s');

        // 1. REGLA ESTRICTA: Validar "Disponibilidad Maestra"
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso; // 1 (Lu) - 7 (Do)
        
        $hasMasterAvailability = DoctorAvailability::where('doctor_id', $doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $start_time)
            ->where('end_time', '>=', $end_time)
            ->exists();

        if (!$hasMasterAvailability) {
            return back()->withErrors(['availability' => 'El especialista no trabaja en ese horario según su Disponibilidad Maestra.'])->withInput();
        }

        // 2. REGLA ESTRICTA: Resolución de Conflictos (Cruce de Horarios)
        $hasConflict = Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $date)
            ->where('status', 'Programado')
            ->where(function ($query) use ($start_time, $end_time) {
                // (StartA <= EndB) and (EndA >= StartB)
                $query->where('start_time', '<', $end_time)
                      ->where('end_time', '>', $start_time);
            })->exists();

        if ($hasConflict) {
            return back()->withErrors(['conflict' => 'El especialista ya tiene una cita Programada que interfiere con este horario exacto.'])->withInput();
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctor_id,
            'appointment_date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => 'Programado',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Cita programada con éxito.');
    }

    public function edit(Appointment $appointment)
    {
        $doctors = User::all();
        return view('appointments.edit', compact('appointment', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $doctor_id = $request->doctor_id;
        $date = $request->appointment_date;
        $start_time = Carbon::parse($request->start_time)->format('H:i:s');
        $end_time = Carbon::parse($request->end_time)->format('H:i:s');

        // 1. Validar Disponibilidad Maestra
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;
        
        $hasMasterAvailability = DoctorAvailability::where('doctor_id', $doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $start_time)
            ->where('end_time', '>=', $end_time)
            ->exists();

        if (!$hasMasterAvailability) {
            return back()->withErrors(['availability' => 'El especialista no trabaja en ese horario según su Disponibilidad Maestra.'])->withInput();
        }

        // 2. Resolución de Conflictos (Ignorando la cita actual)
        $hasConflict = Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $date)
            ->where('status', 'Programado')
            ->where('id', '!=', $appointment->id)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->where('start_time', '<', $end_time)
                      ->where('end_time', '>', $start_time);
            })->exists();

        if ($hasConflict) {
            return back()->withErrors(['conflict' => 'El especialista ya tiene otra cita Programada en ese horario.'])->withInput();
        }

        $appointment->update([
            'doctor_id' => $doctor_id,
            'appointment_date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Cita actualizada con éxito.');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'Cancelado']);
        return redirect()->route('appointments.index')->with('success', 'La cita ha sido cancelada.');
    }
}
