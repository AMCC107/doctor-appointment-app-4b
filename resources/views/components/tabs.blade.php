@props([
    'active' => '',
])

@php
    $active = (string) $active;
@endphp

<div x-data="{ active: @js($active) }">
    @if(isset($header))
        <div class="border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500" role="tablist">
                {{ $header }}
            </ul>
        </div>
    @endif

    <div class="mt-6">
        {{ $slot }}
    </div>
</div>
