<div>
    @if(session('consultation_saved'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            Consulta guardada correctamente.
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Consulta</h1>
            <p class="mt-1 text-lg font-medium text-gray-700 dark:text-gray-300">{{ $appointment->patient->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">DNI: {{ $appointment->patient->user->id_number ?? 'No registrado' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="openHistoryModal" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fa-solid fa-book me-2"></i> Ver Historia
            </button>
            <button type="button" wire:click="openPastConsultationsModal" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Consultas Anteriores
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <x-tabs active="consulta">
            <x-slot name="header">
                <x-tabs-link name="consulta">
                    <i class="fa-solid fa-stethoscope me-2"></i> Consulta
                </x-tabs-link>
                <x-tabs-link name="receta">
                    <i class="fa-solid fa-pills me-2"></i> Receta
                </x-tabs-link>
            </x-slot>

            <x-tab-content name="consulta">
                <div class="space-y-4 p-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diagnóstico</label>
                        <textarea wire:model="diagnosis" rows="4" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Describa el diagnóstico del paciente aquí..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tratamiento</label>
                        <textarea wire:model="treatment" rows="4" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Describa el tratamiento recomendado aquí..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas</label>
                        <textarea wire:model="notes" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Agregue notas adicionales sobre la consulta..."></textarea>
                    </div>
                </div>
            </x-tab-content>

            <x-tab-content name="receta">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Medicamento</th>
                                    <th class="px-4 py-3">Dosis</th>
                                    <th class="px-4 py-3">Frecuencia / Duración</th>
                                    <th class="px-4 py-3 w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medications as $index => $med)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-2">
                                            <input type="text" wire:model="medications.{{ $index }}.medication" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2" placeholder="Ej: Amoxicilina 500mg">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" wire:model="medications.{{ $index }}.dose" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2" placeholder="Ej: 1 cada 8 horas">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" wire:model="medications.{{ $index }}.frequency_duration" class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2" placeholder="Ej: cada 8 horas por 7 días">
                                        </td>
                                        <td class="px-4 py-2">
                                            <button type="button" wire:click="removeMedication({{ $index }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" wire:click="addMedication" class="mt-4 inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500">
                        <i class="fa-solid fa-plus me-2"></i> Añadir Medicamento
                    </button>
                </div>
            </x-tab-content>
        </x-tabs>

        <div class="flex justify-end p-6 border-t border-gray-200 dark:border-gray-700">
            <button type="button" wire:click="saveConsultation" class="inline-flex items-center px-5 py-2.5 text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm dark:bg-purple-600 dark:hover:bg-purple-700">
                <i class="fa-solid fa-save me-2"></i> Guardar Consulta
            </button>
        </div>
    </div>

    {{-- Modal Historia médica del paciente --}}
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeHistoryModal"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historia médica del paciente</h3>
                        <button type="button" wire:click="closeHistoryModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><span class="font-medium">Tipo de sangre:</span> {{ $appointment->patient->bloodType->type ?? 'No registrado' }}</p>
                        <p><span class="font-medium">Alergias:</span> {{ $appointment->patient->allergies ?: 'No registradas' }}</p>
                        <p><span class="font-medium">Enfermedades crónicas:</span> {{ $appointment->patient->chronic_conditions ?: 'No registradas' }}</p>
                        <p><span class="font-medium">Antecedentes quirúrgicos:</span> {{ $appointment->patient->surgical_history ?: 'No registrados' }}</p>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.patients.edit', $appointment->patient) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            Ver / Editar Historia Médica
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Consultas Anteriores --}}
    @if($showPastConsultationsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closePastConsultationsModal"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full max-h-[90vh] flex flex-col">
                    <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Consultas Anteriores</h3>
                        <button type="button" wire:click="closePastConsultationsModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4 overflow-y-auto flex-1">
                        @forelse($this->pastConsultations as $past)
                            <div class="mb-4 p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $past->date->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($past->start_time)->format('H:i') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Atendido por: {{ $past->doctor->user->name ?? 'N/A' }}</p>
                                <p class="mt-2 text-sm"><span class="font-medium">Diagnóstico:</span> {{ \Illuminate\Support\Str::limit($past->diagnosis, 80) }}</p>
                                <p class="text-sm"><span class="font-medium">Tratamiento:</span> {{ \Illuminate\Support\Str::limit($past->treatment, 80) }}</p>
                                @if($past->notes)
                                    <p class="text-sm"><span class="font-medium">Notas:</span> {{ \Illuminate\Support\Str::limit($past->notes, 60) }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay consultas anteriores registradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
