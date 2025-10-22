<x-admin-layout title="Roles | SpamSafe" :breadcrumbs="[
    [
        'name'=> 'Dashboard',
        'route'=> route('admin.dashboard'),
    ],
    [
        'name'=> 'Roles',
    ],
]">
   @livewire('admin.datatables.role-table')
</x-admin-layout>
