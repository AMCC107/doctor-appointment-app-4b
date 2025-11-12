<x-admin-layout title="Editar usuario | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Usuarios', 'href'=> route('admin.users.index')],
    ['name'=> 'Editar'],
]">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-lg">
        @csrf
        @method('PUT')

        <x-input label="Nombre" name="name" value="{{ old('name', $user->name) }}" required />
        <x-input label="Correo electrónico" name="email" type="email" value="{{ old('email', $user->email) }}" required />

        <x-button blue class="mt-4">Actualizar</x-button>
    </form>
</x-admin-layout>
