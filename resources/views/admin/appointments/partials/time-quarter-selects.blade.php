{{--
  Selectores nativos: solo minutos 00, 15, 30, 45 (Chrome no respeta step en el UI de type="time").
  @var string $prefix 'start' | 'end'
  @var int $hourDefault
  @var int $minuteDefault
  @var string $inputClass clases Tailwind del input
--}}
@php
    $hourKey = $prefix . '_hour';
    $minKey = $prefix . '_minute';
    $hour = (int) old($hourKey, $hourDefault);
    $minute = (int) old($minKey, $minuteDefault);
    $quarters = [0, 15, 30, 45];
    if (! in_array($minute, $quarters, true)) {
        $minute = (int) (round($minute / 15) * 15) % 60;
    }
@endphp
<div class="grid grid-cols-2 gap-2">
    <div>
        <label for="{{ $prefix }}_hour" class="sr-only">Hora ({{ $prefix }})</label>
        <select id="{{ $prefix }}_hour" name="{{ $hourKey }}" class="{{ $inputClass }}" required>
            @for ($h = 0; $h < 24; $h++)
                <option value="{{ $h }}" @selected($hour === $h)>{{ str_pad((string) $h, 2, '0', STR_PAD_LEFT) }}</option>
            @endfor
        </select>
    </div>
    <div>
        <label for="{{ $prefix }}_minute" class="sr-only">Minutos ({{ $prefix }})</label>
        <select id="{{ $prefix }}_minute" name="{{ $minKey }}" class="{{ $inputClass }}" required>
            @foreach ($quarters as $m)
                <option value="{{ $m }}" @selected($minute === $m)>{{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }}</option>
            @endforeach
        </select>
    </div>
</div>
