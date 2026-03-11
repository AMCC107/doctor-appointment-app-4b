<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas', 'route' => route('appointments.index')],
    ['name' => 'Editar Cita #' . $appointment->id]
]">

    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
          <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

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
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Editar Cita #{{ $appointment->id }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Reevalúa y modifica la fecha, hora o el especialista asignado a esta cita.</p>

        <form id="search-availability-form" class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
            
            <div class="w-full md:w-1/4">
                <label for="fecha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                        </svg>
                    </div>
                    <input type="date" id="fecha" name="fecha" value="{{ $appointment->appointment_date }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" required>
                </div>
            </div>

            <div class="w-full md:w-1/4">
                <label for="hora" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora</label>
                <select id="hora" name="hora" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" required>
                    <option value="" disabled>Selecciona una hora</option>
                    @for($h=8; $h<=18; $h++)
                        @foreach(['00', '15', '30', '45'] as $m)
                            @php
                                $timeStr = sprintf('%02d:%s', $h, $m);
                                $isSelected = \Carbon\Carbon::parse($appointment->start_time)->format('H:i') == $timeStr ? 'selected' : '';
                            @endphp
                            <option value="{{ $timeStr }}" {{ $isSelected }}>{{ $timeStr }}</option>
                        @endforeach
                    @endfor
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label for="especialidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Especialidad</label>
                <select id="especialidad" name="especialidad" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                    <option value="Todos" selected>Todas las especialidades</option>
                    <option value="Medicina General">Medicina General</option>
                    <option value="Cardiología">Cardiología</option>
                    <option value="Pediatría">Pediatría</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <button type="button" onclick="buscarDoctores()" class="w-full md:w-auto text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-indigo-600 dark:hover:bg-indigo-700 focus:outline-none dark:focus:ring-indigo-800">
                    Re-evaluar y Buscar
                </button>
            </div>
            
        </form>
    </div>

    <!-- Resultados de la búsqueda -->
    <div id="resultados-busqueda" class="hidden">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Doctores Disponibles para reasignar</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="doctores-list">
            <!-- Los doctores aparecerán aquí mediante JS -->
        </div>
    </div>

    <!-- Formulario oculto para agendar la cita final -->
    <form id="booking-form" action="{{ route('appointments.update', $appointment->id) }}" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <!-- Mantenemos el mismo paciente -->
        <input type="hidden" name="doctor_id" id="agendar_doctor_id">
        <input type="hidden" name="appointment_date" id="agendar_fecha">
        <input type="hidden" name="start_time" id="agendar_hora">
        <input type="hidden" name="end_time" id="agendar_hora_fin">
    </form>

    @push('scripts')
    <script>
        // Data inyectada desde el backend
        const allDoctors = @json($doctors ?? []);
        const curDoctorId = {{ $appointment->doctor_id }};

        function buscarDoctores() {
            const fecha = document.getElementById('fecha').value;
            const hora = document.getElementById('hora').value;
            
            if(!fecha || !hora) {
                alert('Por favor selecciona una fecha y hora.');
                return;
            }

            document.getElementById('resultados-busqueda').classList.remove('hidden');
            
            let [h, m] = hora.split(':');
            let dateObj = new Date(2000, 0, 1, parseInt(h), parseInt(m));
            dateObj.setMinutes(dateObj.getMinutes() + 30);
            let horaFin = String(dateObj.getHours()).padStart(2, '0') + ':' + String(dateObj.getMinutes()).padStart(2, '0');

            const lista = document.getElementById('doctores-list');
            lista.innerHTML = '';
            
            if (allDoctors.length === 0) {
                 lista.innerHTML = '<p class="text-gray-500">No hay doctores en el sistema.</p>';
                 return;
            }

            // Mostramos doctores (simulando filtro de backend para vista fluida)
            const displayDoctors = allDoctors.slice(0, 3);
            
            displayDoctors.forEach(doctor => {
                const isCurrent = doctor.id === curDoctorId;
                const badge = isCurrent ? `<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ml-2">Actual</span>` : '';
                
                const card = `
                    <div class="bg-white border ${isCurrent ? 'border-blue-500 shadow-md' : 'border-gray-200 shadow'} rounded-lg dark:bg-gray-800 dark:border-gray-700 p-5">
                        <div class="flex justify-between items-start mb-2">
                             <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">${doctor.name}</h5>
                             ${badge}
                        </div>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Doctor</p>
                        <button onclick="guardarCambios(${doctor.id}, '${fecha}', '${hora}', '${horaFin}')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            Confirmar Cambios
                            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </button>
                    </div>
                `;
                lista.innerHTML += card;
            });
        }

        function guardarCambios(doctorId, fecha, hora, horaFin) {
            if(confirm('¿Deseas guardar los cambios de especialidad y horario en esta cita?')) {
                document.getElementById('agendar_doctor_id').value = doctorId;
                document.getElementById('agendar_fecha').value = fecha;
                document.getElementById('agendar_hora').value = hora;
                document.getElementById('agendar_hora_fin').value = horaFin;
                document.getElementById('booking-form').submit();
            }
        }
        
        // Auto-buscar al abrir
        document.addEventListener('DOMContentLoaded', () => {
             buscarDoctores();
        });
    </script>
    @endpush
</x-admin-layout>
