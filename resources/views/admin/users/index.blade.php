<x-admin-layout title="Usuarios | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Usuarios'],
]">
    <x-slot name="actions">
        <x-wire-button href="{{ route('admin.users.create') }}" blue>
            <i class="fa-solid fa-plus"></i>
            <span class="ml-1">Nuevo</span>
        </x-wire-button>
    </x-slot>
    </div>
</x-admin-layout>

