<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas', 'route' => route('admin.appointments.index')],
    ['name' => 'Editar']
]">

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Por favor corrige los siguientes errores:</span>
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Editar Cita</h3>

        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                    <select id="patient_id" name="patient_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="doctor_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                    <select id="doctor_id" name="doctor_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>{{ $doctor->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                    <input type="date" id="date" name="date" value="{{ old('date', $appointment->date->format('Y-m-d')) }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                @php
                    $timeInputClassEdit = 'bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white';
                    $st = \Carbon\Carbon::parse($appointment->start_time);
                    $et = \Carbon\Carbon::parse($appointment->end_time);
                    $startHourEdit = old('start_hour', (int) $st->format('H'));
                    $startMinEdit = old('start_minute', (int) $st->format('i'));
                    $endHourEdit = old('end_hour', (int) $et->format('H'));
                    $endMinEdit = old('end_minute', (int) $et->format('i'));
                @endphp
                <div>
                    <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora de Inicio</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Solo intervalos de 15 min</p>
                    @include('admin.appointments.partials.time-quarter-selects', [
                        'prefix' => 'start',
                        'hourDefault' => $startHourEdit,
                        'minuteDefault' => $startMinEdit,
                        'inputClass' => $timeInputClassEdit,
                    ])
                </div>
                <div>
                    <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora Final</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Solo intervalos de 15 min</p>
                    @include('admin.appointments.partials.time-quarter-selects', [
                        'prefix' => 'end',
                        'hourDefault' => $endHourEdit,
                        'minuteDefault' => $endMinEdit,
                        'inputClass' => $timeInputClassEdit,
                    ])
                </div>
            </div>
            <div class="mt-6">
                <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo (Opcional)</label>
                <textarea id="reason" name="reason" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Escribe detalles del motivo de la cita aquí...">{{ old('reason', $appointment->reason) }}</textarea>
            </div>
            <div class="flex mt-6 justify-end">
                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5">Guardar cambios</button>
            </div>
        </form>
    </div>

</x-admin-layout>
