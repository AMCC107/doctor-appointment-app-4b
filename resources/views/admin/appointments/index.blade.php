<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas']
]">

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Éxito!</span> {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            {{ session('info') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 mb-4 text-sm text-amber-900 rounded-lg bg-amber-50 dark:bg-gray-800 dark:text-amber-200" role="alert">
            <span class="font-medium">Aviso:</span> {{ session('warning') }}
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Citas</h1>
        <a href="{{ route('admin.appointments.create') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            + Nuevo
        </a>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar" class="rounded-lg border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <select name="per_page" onchange="this.form.submit()" class="rounded-lg border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">Buscar</button>
        </form>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Paciente</th>
                    <th scope="col" class="px-6 py-3">Doctor</th>
                    <th scope="col" class="px-6 py-3">Fecha</th>
                    <th scope="col" class="px-6 py-3">Hora</th>
                    <th scope="col" class="px-6 py-3">Hora fin</th>
                    <th scope="col" class="px-6 py-3">Estado</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $appointment->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $appointment->patient->user->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $appointment->doctor->user->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($appointment->status == 1)
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Programado</span>
                        @elseif($appointment->status == 2)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-blue-400">Completado</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">Cancelado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="inline-flex items-center justify-center p-1.5 text-white bg-blue-500 rounded hover:bg-blue-600 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-400" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="{{ route('admin.appointments.consultation', $appointment) }}" class="inline-flex items-center justify-center p-1.5 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" title="Atender consulta">
                            <i class="fa-solid fa-stethoscope"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No hay citas programadas actualmente.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($appointments->hasPages())
        <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Mostrando {{ $appointments->firstItem() }} a {{ $appointments->lastItem() }} de {{ $appointments->total() }} resultados
            </p>
            <div>
                {{ $appointments->links() }}
            </div>
        </div>
    @endif

</x-admin-layout>
