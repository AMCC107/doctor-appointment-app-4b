<x-admin-layout title="Nuevo usuario | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Usuarios', 'href'=> route('admin.users.index')],
    ['name'=> 'Crear'],
]">
    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-lg">
        @csrf
        <x-input label="Nombre" name="name" required />
        <x-input label="Correo electrónico" name="email" type="email" required />
        <x-input label="Contraseña" name="password" type="password" required />
        <x-input label="Confirmar contraseña" name="password_confirmation" type="password" required />

        <x-button blue class="mt-4">Guardar</x-button>
    </form>
</x-admin-layout>
