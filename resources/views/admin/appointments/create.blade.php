<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas', 'route' => route('admin.appointments.index')],
    ['name' => 'Nuevo']
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

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Nuevo</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Buscar disponibilidad</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Encuentra el horario perfecto para tu cita.</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Asignación manual de fecha y hora (módulo de horarios automáticos pendiente).</p>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6 sticky top-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen de la cita</h2>

                <form action="{{ route('admin.appointments.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Doctor</label>
                        <select id="doctor_id" name="doctor_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">Seleccione un doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>

                    @php
                        $timeInputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white';
                        $startHour = old('start_hour');
                        $startMin = old('start_minute');
                        if ($startHour === null && old('start_time')) {
                            $p = explode(':', old('start_time'));
                            $startHour = (int) ($p[0] ?? 9);
                            $startMin = (int) ($p[1] ?? 0);
                        }
                        $startHour = (int) ($startHour ?? 9);
                        $startMin = (int) ($startMin ?? 0);
                        $endHour = old('end_hour');
                        $endMin = old('end_minute');
                        if ($endHour === null && old('end_time')) {
                            $p = explode(':', old('end_time'));
                            $endHour = (int) ($p[0] ?? 9);
                            $endMin = (int) ($p[1] ?? 15);
                        }
                        $endHour = (int) ($endHour ?? 9);
                        $endMin = (int) ($endMin ?? 15);
                    @endphp
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora inicio</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Solo intervalos de 15 min</p>
                            @include('admin.appointments.partials.time-quarter-selects', [
                                'prefix' => 'start',
                                'hourDefault' => $startHour,
                                'minuteDefault' => $startMin,
                                'inputClass' => $timeInputClass,
                            ])
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora fin</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Solo intervalos de 15 min</p>
                            @include('admin.appointments.partials.time-quarter-selects', [
                                'prefix' => 'end',
                                'hourDefault' => $endHour,
                                'minuteDefault' => $endMin,
                                'inputClass' => $timeInputClass,
                            ])
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duración</span>
                        <p class="text-sm text-gray-600 dark:text-gray-400">15 minutos</p>
                    </div>

                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paciente</label>
                        <select id="patient_id" name="patient_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->user->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-amber-800 dark:text-amber-200 mt-2 rounded bg-amber-50 dark:bg-amber-900/30 p-2">
                            <strong>WhatsApp:</strong> se usa el teléfono del <em>usuario</em> del paciente; si está vacío, el de <em>contacto de emergencia</em>. Sin ninguno de los dos, no se envía mensaje.
                        </p>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo de la cita</label>
                        <textarea id="reason" name="reason" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ej: Chequeo de medicamentos">{{ old('reason') }}</textarea>
                    </div>

                    <button type="submit" class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700">
                        Confirmar cita
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-admin-layout>
