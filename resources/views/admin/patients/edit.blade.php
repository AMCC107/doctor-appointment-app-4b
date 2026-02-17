@php
    $antecedentesFields = ['allergies', 'chronic_conditions', 'surgical_history', 'family_history'];
    $infoFields = ['blood_type_id', 'observations'];
    $contactoFields = ['emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship'];
@endphp

<x-admin-layout title="Pacientes | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Pacientes', 'href'=> route('admin.patients.index')],
    ['name'=> 'Editar'],
]">

    <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Encabezado --}}
        <x-wire-card class="mb-8">
            <div class="lg:flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <img src="{{ $patient->user->profile_photo_url }}"
                         alt="{{ $patient->user->name }}"
                         class="w-20 h-20 rounded-full object-cover">

                    <p class="text-2xl font-bold text-gray-900">
                        {{ $patient->user->name }}
                    </p>
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

        {{-- Tabs --}}
        <x-wire-card>
            <x-tabs :active="$initialTab">

                {{-- Header --}}
                <x-slot name="header">
                    <x-tabs-link name="datos_personales">
                        <i class="fa-solid fa-user me-2"></i>
                        Datos personales
                    </x-tabs-link>

                    <x-tabs-link
                        name="antecedentes"
                        :error="$errors->hasAny($antecedentesFields)">
                        <i class="fa-solid fa-file-lines me-2"></i>
                        Antecedentes
                    </x-tabs-link>

                    <x-tabs-link
                        name="informacion_general"
                        :error="$errors->hasAny($infoFields)">
                        <i class="fa-solid fa-info me-2"></i>
                        Información general
                    </x-tabs-link>

                    <x-tabs-link
                        name="contacto_emergencia"
                        :error="$errors->hasAny($contactoFields)">
                        <i class="fa-solid fa-heart me-2"></i>
                        Contacto de emergencia
                    </x-tabs-link>
                </x-slot>

                {{-- TAB 1: Datos personales --}}
                <x-tab-content name="datos_personales">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                        <div class="flex justify-between items-center gap-4">
                            <div class="flex gap-4">
                                <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
                                <div>
                                    <h3 class="text-blue-800 font-bold">
                                        Edición de usuario
                                    </h3>
                                    <p class="text-sm text-blue-700 mt-1">
                                        La información de acceso se gestiona desde la cuenta del usuario.
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('admin.users.edit', $patient->user) }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                Editar usuario
                                <i class="fa-solid fa-arrow-up-right-from-square ml-2"></i>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-500 font-semibold text-sm">Teléfono</label>
                            <p>{{ $patient->user->phone ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="text-gray-500 font-semibold text-sm">Email</label>
                            <p>{{ $patient->user->email ?? 'N/A' }}</p>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="text-gray-500 font-semibold text-sm">Dirección</label>
                            <p>{{ $patient->user->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </x-tab-content>

                {{-- TAB 2: Antecedentes --}}
                <x-tab-content name="antecedentes">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <x-wire-textarea name="allergies" label="Alergias">
                            {{ old('allergies', $patient->allergies) }}
                        </x-wire-textarea>

                        <x-wire-textarea name="chronic_conditions" label="Enfermedades crónicas">
                            {{ old('chronic_conditions', $patient->chronic_conditions) }}
                        </x-wire-textarea>

                        <x-wire-textarea name="surgical_history" label="Antecedentes quirúrgicos">
                            {{ old('surgical_history', $patient->surgical_history) }}
                        </x-wire-textarea>

                        <x-wire-textarea name="family_history" label="Antecedentes familiares">
                            {{ old('family_history', $patient->family_history) }}
                        </x-wire-textarea>
                    </div>
                </x-tab-content>

                {{-- TAB 3: Información general --}}
                <x-tab-content name="informacion_general">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <x-wire-native-select name="blood_type_id" label="Tipo de sangre">
                            <option value="">Selecciona un tipo de sangre</option>
                            @foreach ($bloodTypes as $bloodType)
                                <option
                                    value="{{ $bloodType->id }}"
                                    @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
                                    {{ $bloodType->type }}
                                </option>
                            @endforeach
                        </x-wire-native-select>

                        <div class="lg:col-span-2">
                            <x-wire-textarea
                                name="observations"
                                label="Observaciones"
                                rows="4">
                                {{ old('observations', $patient->observations) }}
                            </x-wire-textarea>
                        </div>
                    </div>
                </x-tab-content>

                {{-- TAB 4: Contacto de emergencia --}}
                <x-tab-content name="contacto_emergencia">
                    <div class="space-y-4">
                        <div>
                            <x-wire-input
                                name="emergency_contact_name"
                                label="Nombre del contacto"
                                maxlength="50"
                                :value="old('emergency_contact_name', $patient->emergency_contact_name)" />
                            <p class="mt-1 text-sm text-gray-500" data-char-hint data-for="emergency_contact_name" data-max="50" aria-live="polite">
                                <span data-count>{{ strlen(old('emergency_contact_name', $patient->emergency_contact_name ?? '')) }}</span>/50 caracteres
                                <span data-limit-msg class="hidden"> — Límite de 50 caracteres alcanzado.</span>
                            </p>
                        </div>

                        <x-wire-phone
                            name="emergency_contact_phone"
                            label="Teléfono del contacto"
                            mask="(###) ###-####"
                            :value="old('emergency_contact_phone', $patient->emergency_contact_phone)" />

                        <div>
                            <x-wire-input
                                name="emergency_contact_relationship"
                                label="Relación con el contacto"
                                maxlength="50"
                                :value="old('emergency_contact_relationship', $patient->emergency_contact_relationship)" />
                            <p class="mt-1 text-sm text-gray-500" data-char-hint data-for="emergency_contact_relationship" data-max="50" aria-live="polite">
                                <span data-count>{{ strlen(old('emergency_contact_relationship', $patient->emergency_contact_relationship ?? '')) }}</span>/50 caracteres
                                <span data-limit-msg class="hidden"> — Límite de 50 caracteres alcanzado.</span>
                            </p>
                        </div>
                    </div>
                </x-tab-content>

            </x-tabs>
        </x-wire-card>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var limitFields = ['emergency_contact_name', 'emergency_contact_relationship'];
            limitFields.forEach(function (fieldName) {
                var input = document.querySelector('input[name="' + fieldName + '"]');
                var hint = document.querySelector('[data-char-hint][data-for="' + fieldName + '"]');
                if (!input || !hint) return;
                var max = parseInt(hint.getAttribute('data-max'), 10);
                var countEl = hint.querySelector('[data-count]');
                var limitMsg = hint.querySelector('[data-limit-msg]');
                function update() {
                    var len = input.value.length;
                    countEl.textContent = len;
                    if (len >= max) {
                        hint.classList.remove('text-gray-500');
                        hint.classList.add('text-red-600');
                        if (limitMsg) limitMsg.classList.remove('hidden');
                    } else {
                        hint.classList.remove('text-red-600');
                        hint.classList.add('text-gray-500');
                        if (limitMsg) limitMsg.classList.add('hidden');
                    }
                }
                input.addEventListener('input', update);
                input.addEventListener('change', update);
                update();
            });
        });
    </script>

</x-admin-layout>