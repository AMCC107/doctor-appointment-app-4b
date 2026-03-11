<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DoctorAvailability;
use App\Models\Appointment;
use Carbon\Carbon;

class DoctorAvailabilityController extends Controller
{
    public function edit(Request $request)
    {
        // En vez de tener un 1 quemado, ahora lo tomamos por query string o default a 1.
        $doctorId = $request->get('doctor_id', 1);

        $availabilities = DoctorAvailability::where('doctor_id', $doctorId)->get();

        return view('doctor_availabilities.edit', compact('availabilities', 'doctorId'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'availabilities' => 'array',
            // Example format of availabilities:
            // [ 'day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '10:00' ], ...
        ]);

        $doctorId = $request->doctor_id;

        // Delete old availabilities for this doctor
        DoctorAvailability::where('doctor_id', $doctorId)->delete();

        if ($request->has('availabilities') && is_array($request->availabilities)) {
            $insertData = [];
            foreach ($request->availabilities as $avail) {
                // Ensure proper formatting and create records
                $insertData[] = [
                    'doctor_id' => $doctorId,
                    'day_of_week' => $avail['day_of_week'],
                    'start_time' => Carbon::parse($avail['start_time'])->format('H:i:s'),
                    'end_time' => Carbon::parse($avail['end_time'])->format('H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if(!empty($insertData)){
               DoctorAvailability::insert($insertData);
            }
        }

        return redirect()->back()->with('success', 'Horarios actualizados correctamente.');
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        $availabilities = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($availabilities->isEmpty()) {
            return response()->json([]); // No availability on this day
        }

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'Cancelado')
            ->get();

        $availableSlots = [];
        $slotDuration = 15; // in minutes

        foreach ($availabilities as $avail) {
            $start = Carbon::parse($avail->start_time);
            $end = Carbon::parse($avail->end_time);

            while ($start->copy()->addMinutes($slotDuration)->lte($end)) {
                $slotStart = $start->format('H:i:s');
                $slotEnd = $start->copy()->addMinutes($slotDuration)->format('H:i:s');
                
                $isConflict = false;
                foreach ($appointments as $appt) {
                    $apptStart = Carbon::parse($appt->start_time)->format('H:i:s');
                    $apptEnd = Carbon::parse($appt->end_time)->format('H:i:s');
                    
                    // Logic to check overlap for this specific 15 min slot
                    if ($slotStart < $apptEnd && $slotEnd > $apptStart) {
                        $isConflict = true;
                        break;
                    }
                }

                if (!$isConflict) {
                    $availableSlots[] = [
                        'start' => $slotStart,
                        'end' => $slotEnd
                    ];
                }

                $start->addMinutes($slotDuration);
            }
        }

        return response()->json($availableSlots);
    }
}
