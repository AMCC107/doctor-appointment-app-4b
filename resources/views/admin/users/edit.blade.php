<x-admin-layout title="Editar usuario | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Usuarios', 'href'=> route('admin.users.index')],
    ['name'=> 'Editar'],
]">

    {{-- 1. Título dinámico para saber a quién editamos --}}
    <x-wire-card title="Editar Usuario: {{ $user->name }}">
        
        {{-- 2. Corrección: El method del form HTML siempre debe ser POST. El PUT se define con @method --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                
                {{-- Datos de Cuenta --}}
                <x-wire-input label="Nombre" name="name" required :value="old('name', $user->name)" placeholder="Nombre completo" autocomplete="name" />
                
                <x-wire-input label="Correo electrónico" name="email" type="email" required :value="old('email', $user->email)" placeholder="correo@ejemplo.com" autocomplete="email" />

                {{-- Nota: En edición, el password no suele ser required (solo si se quiere cambiar) --}}
                <x-wire-input label="Contraseña" name="password" type="password" placeholder="Dejar en blanco para mantener la actual" autocomplete="new-password" />
                
                <x-wire-input label="Confirmar contraseña" name="password_confirmation" type="password" placeholder="Confirmar solo si cambias la contraseña" autocomplete="new-password" />

                {{-- Datos Personales --}}
                <x-wire-input name="id_number" label="Número de ID" required :value="old('id_number', $user->id_number)" placeholder="Ej. 123456789" inputmode="numeric" />
                
                <x-wire-input name="phone" label="Teléfono" required :value="old('phone', $user->phone)" placeholder="Ej. 555-123-4567" autocomplete="tel" inputmode="tel" />
            
            </div>

            {{-- Campos de ancho completo --}}
            <div class="space-y-4">
                <x-wire-input name="address" label="Dirección" required :value="old('address', $user->address)" placeholder="Ej. Calle 123, Colonia Centro" autocomplete="street-address" />

                <div>
                    <x-wire-native-select name="role_id" label="Rol" required>
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $role)
                            {{-- 3. Mejora: Usamos ?->id para evitar error si el usuario no tiene rol asignado --}}
                            <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>
                                {{ $role->name }}
                            </option>   
                        @endforeach
                    </x-wire-native-select>
                    <p class="text-sm text-gray-500 mt-1">
                        Define los permisos y accesos del usuario dentro del sistema.
                    </p>
                </div>
            </div>

            {{-- Área de acciones --}}
            <div class="flex justify-end items-center gap-2 mt-6 pt-4 border-t border-gray-200">
                <x-wire-button href="{{ route('admin.users.index') }}" flat label="Cancelar" />
                <x-wire-button type="submit" blue label="Actualizar Usuario" />
            </div>

        </form>
    </x-wire-card>

</x-admin-layout>