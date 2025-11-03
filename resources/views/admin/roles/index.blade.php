<x-admin-layout title="Roles | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Roles'],
]">

    <x-slot name="actions">
        <x-wire-button href="{{route('admin.roles.create')}}" blue>
            <i class="fa-solid fa-plus"></i>
            <span class="ml-1">Nuevo</span>
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.role-table')
</x-admin-layout>
