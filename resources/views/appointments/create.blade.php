<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas', 'route' => route('appointments.index')],
    ['name' => 'Nuevo']
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
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Buscar disponibilidad</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Encuentra el horario perfecto para tu cita.</p>

        <form id="search-availability-form" class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
            
            <div class="w-full md:w-1/4">
                <label for="fecha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                        </svg>
                    </div>
                    <input type="date" id="fecha" name="fecha" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" required>
                </div>
            </div>

            <div class="w-full md:w-1/4">
                <label for="hora" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora</label>
                <select id="hora" name="hora" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" required>
                    <option value="" disabled selected>Selecciona una hora</option>
                    @for($h=8; $h<=18; $h++)
                        <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                        <option value="{{ sprintf('%02d:15', $h) }}">{{ sprintf('%02d:15', $h) }}</option>
                        <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
                        <option value="{{ sprintf('%02d:45', $h) }}">{{ sprintf('%02d:45', $h) }}</option>
                    @endfor
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label for="especialidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Especialidad (opcional)</label>
                <select id="especialidad" name="especialidad" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                    <option value="Todos" selected>Todas las especialidades</option>
                    @foreach($specialties as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-auto">
                <button type="button" onclick="buscarDoctores()" class="w-full md:w-auto text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-indigo-600 dark:hover:bg-indigo-700 focus:outline-none dark:focus:ring-indigo-800">
                    Buscar disponibilidad
                </button>
            </div>
            
        </form>
    </div>

    <!-- Resultados de la búsqueda -->
    <div id="resultados-busqueda" class="hidden">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Doctores Disponibles</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="doctores-list">
            <!-- Los doctores aparecerán aquí mediante JS -->
        </div>
    </div>

    <!-- Formulario oculto para agendar la cita final -->
    <form id="booking-form" action="{{ route('appointments.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="patient_id" value="1"> <!-- Asumimos paciente ID 1 por defecto para pruebas -->
        <input type="hidden" name="doctor_id" id="agendar_doctor_id">
        <input type="hidden" name="appointment_date" id="agendar_fecha">
        <input type="hidden" name="start_time" id="agendar_hora">
        <input type="hidden" name="end_time" id="agendar_hora_fin">
    </form>

    @push('scripts')
    <script>
        // Token CSRF para las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function buscarDoctores() {
            const fecha = document.getElementById('fecha').value;
            const hora = document.getElementById('hora').value;
            const especialidad = document.getElementById('especialidad').value;
            
            if(!fecha || !hora) {
                alert('Por favor selecciona una fecha y hora.');
                return;
            }

            const boton = document.querySelector('#search-availability-form button');
            const lista = document.getElementById('doctores-list');
            
            boton.disabled = true;
            boton.innerHTML = 'Buscando...';
            document.getElementById('resultados-busqueda').classList.remove('hidden');
            lista.innerHTML = '<p class="text-gray-500 col-span-full text-center">Consultando disponibilidad en tiempo real...</p>';

            try {
                const response = await fetch('{{ route("appointments.check_availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        date: fecha,
                        time: hora,
                        specialty_id: especialidad === 'Todos' ? null : especialidad
                    })
                });

                const data = await response.json();
                
                lista.innerHTML = '';

                if(response.ok && data.success) {
                    if (data.slots.length === 0) {
                         lista.innerHTML = '<p class="text-gray-500 col-span-full">No hay doctores con disponibilidad maestra para esa fecha/hora, o todos tienen sus agendas ocupadas.</p>';
                    } else {
                        data.slots.forEach(slot => {
                            const card = `
                                <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-5">
                                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">${slot.doctor_name}</h5>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">${slot.specialty}</p>
                                    <p class="mb-3 text-sm text-gray-500 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i> ${slot.start_time} - ${slot.end_time}</p>
                                    <button onclick="agendarCita(${slot.doctor_id}, '${fecha}', '${slot.start_time}', '${slot.end_time}')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-green-500 rounded-lg hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-300">
                                        Seleccionar y Agendar
                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                    </button>
                                </div>
                            `;
                            lista.innerHTML += card;
                        });
                    }
                } else {
                    const errorMsg = data.message || 'Error al procesar la solicitud.';
                    lista.innerHTML = `<p class="text-red-500 col-span-full">${errorMsg}</p>`;
                }

            } catch (error) {
                console.error(error);
                lista.innerHTML = '<p class="text-red-500 col-span-full">Hubo un problema de conexión con el servidor.</p>';
            } finally {
                boton.disabled = false;
                boton.innerHTML = 'Buscar disponibilidad';
            }
        }

        function agendarCita(doctorId, fecha, hora, horaFin) {
            if(confirm('¿Deseas confirmar la cita para la fecha y hora seleccionada?')) {
                document.getElementById('agendar_doctor_id').value = doctorId;
                document.getElementById('agendar_fecha').value = fecha;
                document.getElementById('agendar_hora').value = hora;
                document.getElementById('agendar_hora_fin').value = horaFin;
                
                // Mantiene el ID de paciente 1 provisto previamente (por propósitos de demostración)
                document.querySelector('input[name="patient_id"]').value = 1;

                document.getElementById('booking-form').submit();
            }
        }
    </script>
    @endpush
</x-admin-layout>
