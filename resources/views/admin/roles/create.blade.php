<x-admin-layout 
    title="Roles | SpamSafe" 
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Roles',
            'href' => route('admin.roles.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]"
>

    <x-slot name="actions">
        <x-wire-button href="{{ route('admin.roles.index') }}" gray>
            <i class="fa-solid fa-arrow-left"></i>
            Regresar
        </x-wire-button>
    </x-slot>

    {{-- Aquí puedes agregar el formulario para crear roles --}}

</x-admin-layout>
