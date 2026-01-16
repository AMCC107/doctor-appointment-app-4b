<x-admin-layout title="Nuevo usuario | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Usuarios', 'href'=> route('admin.users.index')],
    ['name'=> 'Crear'],
]">

    {{-- Usamos x-wire-card para contener todo el formulario de forma limpia --}}
    <x-wire-card title="Registrar Nuevo Usuario">
        
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            {{-- Grid principal para organizar los campos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                
                {{-- Datos de Cuenta --}}
                <x-wire-input label="Nombre" name="name" required :value="old('name')" placeholder="Nombre completo" autocomplete="name" />
                
                <x-wire-input label="Correo electrónico" name="email" type="email" required :value="old('email')" placeholder="correo@ejemplo.com" autocomplete="email" />

                <x-wire-input label="Contraseña" name="password" type="password" required placeholder="Mínimo 8 caracteres" autocomplete="new-password" />
                
                <x-wire-input label="Confirmar contraseña" name="password_confirmation" type="password" required placeholder="Repite la contraseña" autocomplete="new-password" />

                {{-- Datos Personales --}}
                <x-wire-input name="id_number" label="Número de ID" required :value="old('id_number')" placeholder="Ej. 123456789" inputmode="numeric" />
                
                <x-wire-input name="phone" label="Teléfono" required :value="old('phone')" placeholder="Ej. 555-123-4567" autocomplete="tel" inputmode="tel" />
            
            </div>

            {{-- Campos de ancho completo --}}
            <div class="space-y-4">
                <x-wire-input name="address" label="Dirección" required :value="old('address')" placeholder="Ej. Calle 123, Colonia Centro" autocomplete="street-address" />

                <div>
                    <x-wire-native-select name="role_id" label="Rol" required>
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                {{ $role->name }}
                            </option>   
                        @endforeach
                    </x-wire-native-select>
                    <p class="text-sm text-gray-500 mt-1">
                        Define los permisos y accesos del usuario dentro del sistema.
                    </p>
                </div>
            </div>

            {{-- Área de acciones (Botones) --}}
            <div class="flex justify-end items-center gap-2 mt-6 pt-4 border-t border-gray-200">
                <x-wire-button href="{{ route('admin.users.index') }}" flat label="Cancelar" />
                <x-wire-button type="submit" blue label="Guardar Usuario" />
            </div>

        </form>
    </x-wire-card>

</x-admin-layout>