<x-admin-layout title="Roles | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Roles'],
]">

    {{-- Botón "Nuevo --}}
    <x-slot name="actions">
        <div class="flex justify-end">
            <x-wire-button href="{{ route('admin.roles.create') }}" blue>
                <i class="fa-solid fa-plus"></i>
                <span class="ml-1">Nuevo</span>
            </x-wire-button>
        </div>
    </x-slot>

    {{-- Tabla Livewire --}}
    @livewire('admin.datatables.role-table')

</x-admin-layout>
