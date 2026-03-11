<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Citas', 'route' => route('admin.appointments.index')],
    ['name' => 'Consulta']
]">
    @livewire('admin.consultation-manager', ['appointmentId' => $appointment->id])
</x-admin-layout>
