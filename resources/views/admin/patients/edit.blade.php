<x-admin-layout title="Pacientes | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Pacientes', 'href'=> route('admin.patients.index')],
    ['name'=> 'Editar'],
]">

    <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card class="mb-8">
            {{-- Encabezado --}}
            <div class="lg:flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ $patient->user->profile_photo_url }}"
                         alt="{{ $patient->user->name }}"
                         class="w-20 h-20 rounded-full object-cover object-center">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $patient->user->name }}</p>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.patients.index') }}">
                        Volver
                    </x-wire-button>

                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Tabs de navegación --}}
        <x-wire-card>
            <div x-data="{ tab: 'datos-personales' }">

                {{-- Menú de pestañas --}}
                <div class="border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">

                        {{-- Tab 1: Datos personales --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'datos-personales'"
                               :class="{
                                   'text-blue-600 border-blue-600 active': tab === 'datos-personales',
                                   'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'datos-personales',
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'datos-personales' ? 'page' : undefined">
                                <i class="fa-solid fa-user me-2"></i>
                                Datos personales
                            </a>
                        </li>

                        {{-- Tab 2: Antecedentes --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'antecedentes'"
                               :class="{
                                   'text-blue-600 border-blue-600 active': tab === 'antecedentes',
                                   'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'antecedentes',
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'antecedentes' ? 'page' : undefined">
                                <i class="fa-solid fa-file-lines me-2"></i>
                                Antecedentes
                            </a>
                        </li>

                        {{-- Tab 3: Información general --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'informacion-general'"
                               :class="{
                                   'text-blue-600 border-blue-600 active': tab === 'informacion-general',
                                   'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'informacion-general',
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'informacion-general' ? 'page' : undefined">
                                <i class="fa-solid fa-info me-2"></i>
                                Información general
                            </a>
                        </li>

                        {{-- Contacto de emergencia --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'contacto-emergencia'"
                               :class="{
                                   'text-blue-600 border-blue-600 active': tab === 'contacto-emergencia',
                                   'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'contacto-emergencia',
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'contacto-emergencia' ? 'page' : undefined">
                                <i class="fa-solid fa-heart me-2"></i>
                                Contacto de emergencia
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Contenido de los tabs --}}
                <div class="mt-6">

                    {{-- Tab 1: Datos Personales --}}
                    <div x-show="tab === 'datos-personales'">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                            <div class="flex justify-between items-center gap-4">
                                <div class="flex gap-4">
                                    <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
                                    <div>
                                        <h3 class="text-blue-800 font-bold">Edición de usuario</h3>
                                        <p class="text-sm text-blue-700 mt-1">
                                            La información de acceso (nombre, email y contraseña) debe gestionarse desde la cuenta de usuario asociada.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.edit', $patient->user) }}" target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                                        Editar usuario
                                        <i class="fa-solid fa-arrow-up-right-from-square ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="text-gray-500 font-semibold text-sm">Teléfono</label>
                                <p class="text-gray-900">{{ $patient->user->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 font-semibold text-sm">Email</label>
                                <p class="text-gray-900">{{ $patient->user->email ?? 'N/A' }}</p>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-gray-500 font-semibold text-sm">Dirección</label>
                                <p class="text-gray-900">{{ $patient->user->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 2: Antecedentes --}}
                    <div x-show="tab === 'antecedentes'">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            <x-wire-textarea name="allergies" label="Alergias" rows="4">
                                {{ old('allergies', $patient->allergies) }}
                            </x-wire-textarea>

                            <x-wire-textarea name="chronic_conditions" label="Enfermedades Crónicas" rows="4">
                                {{ old('chronic_conditions', $patient->chronic_conditions) }}
                            </x-wire-textarea>

                            <x-wire-textarea name="surgical_history" label="Antecedentes Quirúrgicos" rows="4">
                                {{ old('surgical_history', $patient->surgical_history) }}
                            </x-wire-textarea>

                            <x-wire-textarea name="family_history" label="Antecedentes Familiares" rows="4">
                                {{ old('family_history', $patient->family_history) }}
                            </x-wire-textarea>

                        </div>
                    </div>


                    {{-- Tab 3: Información General --}}
                    <div x-show="tab === 'informacion-general'">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <x-wire-native-select name="blood_type_id" label="Tipo de Sangre">
                                <option value="">Selecciona un tipo de sangre</option>
                                @foreach ($bloodTypes as $bloodType)
                                    <option value="{{ $bloodType->id }}" @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
                                        {{ $bloodType->name }}
                                    </option>
                                @endforeach
                            </x-wire-native-select>

                            <div class="lg:col-span-2">
                                <x-wire-textarea name="observations" label="Observaciones" :value="old('observations', $patient->observations)" rows="4"/>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 4: Contacto de Emergencia --}}
                    <div x-show="tab === 'contacto-emergencia'">
                        <div class="space-y-4">
                            <x-wire-input name="emergency_contact_name" label="Nombre de contacto" :value="old('emergency_contact_name', $patient->emergency_contact_name)"/>
                            <x-wire-phone name="emergency_contact_phone" label="Teléfono de contacto" :value="old('emergency_contact_phone', $patient->emergency_contact_phone)" mask="(###) ###-####" placeholder="(999) 999-9999"/>
                            <x-wire-input name="emergency_contact_relationship" label="Relación con el contacto" :value="old('emergency_contact_relationship', $patient->emergency_contact_relationship)" placeholder="Familiar, Amigo, etc"/>
                        </div>
                    </div>

                </div>
            </div>
        </x-wire-card>
    </form>

</x-admin-layout>