<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Horarios']
]">

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
          <span class="font-medium">Éxito!</span> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        
        <form id="schedule-form" action="{{ route('doctor_availabilities.update') }}" method="POST">
            @csrf
            <input type="hidden" name="doctor_id" value="{{ $doctorId }}">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Gestor de horarios</h3>
                <button type="submit" class="mt-4 md:mt-0 text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-indigo-600 dark:hover:bg-indigo-700 focus:outline-none dark:focus:ring-indigo-800">
                    Guardar horario
                </button>
            </div>

            <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700 pt-4">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-400 uppercase bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-2 py-3 font-semibold pb-4">DÍA/HORA</th>
                            @php
                                $days = [
                                    1 => 'LUNES', 
                                    2 => 'MARTES', 
                                    3 => 'MIÉRCOLES', 
                                    4 => 'JUEVES', 
                                    5 => 'VIERNES', 
                                    6 => 'SÁBADO', 
                                    7 => 'DOMINGO'
                                ];
                            @endphp
                            @foreach($days as $dayK => $dayV)
                                <th scope="col" class="px-2 py-3 font-semibold pb-4">{{ $dayV }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $startHour = 8; // 08:00
                            $endHour = 18;  // 18:00
                        @endphp
                        
                        @for($h = $startHour; $h < $endHour; $h++)
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 align-top">
                                <!-- Hour Label -->
                                <th scope="row" class="px-2 py-6 font-medium text-gray-900 whitespace-nowrap dark:text-white w-48">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="hour-toggle-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 mr-3" data-hour="{{ $h }}">
                                        <span class="text-base">{{ sprintf('%02d:00:00', $h) }}</span>
                                    </div>
                                </th>
                                
                                <!-- Scopes per Day -->
                                @foreach($days as $dayK => $dayV)
                                    <td class="px-2 py-4">
                                        <div class="flex flex-col space-y-3">
                                            <!-- Todos Checkbox -->
                                            <div class="flex items-center">
                                                <input type="checkbox" class="day-hour-toggle-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" data-day="{{ $dayK }}" data-hour="{{ $h }}">
                                                <label class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300">Todos</label>
                                            </div>
                                            
                                            <!-- 15-min intervals -->
                                            @foreach(['00', '15', '30', '45'] as $m)
                                                @php
                                                    $timeStr = sprintf('%02d:%s', $h, $m);
                                                    $endTimeCarbon = \Carbon\Carbon::parse($timeStr)->addMinutes(15);
                                                    $timeEndStr = $endTimeCarbon->format('H:i');
                                                    
                                                    // Determine if checked in DB
                                                    $isChecked = false;
                                                    foreach($availabilities as $avail) {
                                                        if(
                                                            $avail->day_of_week == $dayK && 
                                                            \Carbon\Carbon::parse($avail->start_time)->format('H:i') <= $timeStr && 
                                                            \Carbon\Carbon::parse($avail->end_time)->format('H:i') >= $timeEndStr
                                                        ) {
                                                            $isChecked = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <div class="flex items-center pl-1">
                                                    <input type="checkbox" 
                                                           data-day="{{ $dayK }}" 
                                                           data-hour="{{ $h }}"
                                                           data-start="{{ $timeStr }}" 
                                                           data-end="{{ $timeEndStr }}" 
                                                           class="time-slot-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $timeStr }} - {{ $timeEndStr }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Master logic for "Hour Checkbox" (e.g. 08:00:00 checks everything in that row)
            document.querySelectorAll('.hour-toggle-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    let hour = this.getAttribute('data-hour');
                    let isChecked = this.checked;
                    document.querySelectorAll(`.day-hour-toggle-checkbox[data-hour="${hour}"]`).forEach(dayCb => dayCb.checked = isChecked);
                    document.querySelectorAll(`.time-slot-checkbox[data-hour="${hour}"]`).forEach(slotCb => slotCb.checked = isChecked);
                });
            });

            // Master logic for "Todos" Checkbox (checks all 4 slots in that day/hour)
            document.querySelectorAll('.day-hour-toggle-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    let hour = this.getAttribute('data-hour');
                    let day = this.getAttribute('data-day');
                    let isChecked = this.checked;
                    document.querySelectorAll(`.time-slot-checkbox[data-hour="${hour}"][data-day="${day}"]`).forEach(slotCb => slotCb.checked = isChecked);
                });
            });
            
            // Sync up "Todos" and "Hour" when individual 15-min slots are selected/deselected
            document.querySelectorAll('.time-slot-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    let hour = this.getAttribute('data-hour');
                    let day = this.getAttribute('data-day');
                    
                    let allSlotsForDayAndHour = Array.from(document.querySelectorAll(`.time-slot-checkbox[data-hour="${hour}"][data-day="${day}"]`));
                    let dayHourCb = document.querySelector(`.day-hour-toggle-checkbox[data-hour="${hour}"][data-day="${day}"]`);
                    
                    if (dayHourCb) {
                         dayHourCb.checked = allSlotsForDayAndHour.every(chk => chk.checked);
                    }
                    
                    let allSlotsForHour = Array.from(document.querySelectorAll(`.time-slot-checkbox[data-hour="${hour}"]`));
                    let hourCb = document.querySelector(`.hour-toggle-checkbox[data-hour="${hour}"]`);
                    
                    if(hourCb) {
                         hourCb.checked = allSlotsForHour.every(chk => chk.checked);
                    }
                });
            });
            
            // Initial sync
            document.querySelectorAll('.time-slot-checkbox').forEach(cb => {
                cb.dispatchEvent(new Event('change'));
            });
            
            // Form Submit Logic for aggregating contiguous slots
            document.getElementById('schedule-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const checkboxes = document.querySelectorAll('.time-slot-checkbox:checked');
                
                let selectionsByDay = {};
                
                checkboxes.forEach(cb => {
                    const day = cb.getAttribute('data-day');
                    const start = cb.getAttribute('data-start');
                    const end = cb.getAttribute('data-end');
                    
                    if (!selectionsByDay[day]) {
                        selectionsByDay[day] = [];
                    }
                    selectionsByDay[day].push({ start: start, end: end });
                });
                
                let groupedAvailabilities = [];
                
                for (let day in selectionsByDay) {
                    let slots = selectionsByDay[day].sort((a, b) => a.start.localeCompare(b.start));
                    if(slots.length === 0) continue;
                    
                    let currentBlock = { ...slots[0] };
                    
                    for (let i = 1; i < slots.length; i++) {
                        let nextSlot = slots[i];
                        if (currentBlock.end === nextSlot.start) {
                            currentBlock.end = nextSlot.end; // merge
                        } else {
                            groupedAvailabilities.push({
                                day_of_week: day,
                                start_time: currentBlock.start,
                                end_time: currentBlock.end
                            });
                            currentBlock = { ...nextSlot };
                        }
                    }
                    groupedAvailabilities.push({
                        day_of_week: day,
                        start_time: currentBlock.start,
                        end_time: currentBlock.end
                    });
                }
                
                groupedAvailabilities.forEach((avail, index) => {
                    const i1 = document.createElement('input'); i1.type = 'hidden'; i1.name = `availabilities[${index}][day_of_week]`; i1.value = avail.day_of_week; form.appendChild(i1);
                    const i2 = document.createElement('input'); i2.type = 'hidden'; i2.name = `availabilities[${index}][start_time]`; i2.value = avail.start_time; form.appendChild(i2);
                    const i3 = document.createElement('input'); i3.type = 'hidden'; i3.name = `availabilities[${index}][end_time]`; i3.value = avail.end_time; form.appendChild(i3);
                });
                
                form.submit();
            });
        });
    </script>
    @endpush
</x-admin-layout>
