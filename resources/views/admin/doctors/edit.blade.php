<x-admin-layout title="Doctores | SpamSafe" :breadcrumbs="[
    ['name'=> 'Dashboard', 'href'=> route('admin.dashboard')],
    ['name'=> 'Doctores', 'href'=> route('admin.doctors.index')],
    ['name'=> 'Editar'],
]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card class="mb-8">
            <div class="lg:flex justify-between items-center">
                <div class="flex items-center gap-4">
                    @if(optional($doctor->user)->profile_photo_url)
                        <img src="{{ $doctor->user->profile_photo_url }}"
                             alt="{{ $doctor->user->name }}"
                             class="w-20 h-20 rounded-full object-cover">
                    @endif
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ optional($doctor->user)->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Licencia: <span class="font-semibold">{{ $doctor->medical_license ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit" primary>
                        <i class="fa-solid fa-check me-2"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        <x-wire-card>
            <div class="space-y-6">

                <x-wire-native-select name="specialty_id" label="Especialidad" required>
                    @foreach ($specialties as $s)
                        <option value="{{ $s->id }}" {{ (string) old('specialty_id', $doctor->specialty_id) === (string) $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>

                <div>
                    <x-wire-input
                        name="medical_license"
                        label="Licencia médica"
                        :value="old('medical_license', $doctor->medical_license)"
                        maxlength="25"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografía</label>
                    <p class="text-sm text-gray-500 mb-2">Actual: {{ $doctor->bio ?? 'N/A' }}</p>
                    <x-wire-textarea
                        name="bio"
                        label=""
                        rows="4"
                        placeholder="Escriba la biografía del doctor"
                    >{{ old('bio', $doctor->bio ?? '') }}</x-wire-textarea>
                    <x-input-error for="bio" class="mt-1" />
                </div>

            </div>
        </x-wire-card>
    </form>

</x-admin-layout>
