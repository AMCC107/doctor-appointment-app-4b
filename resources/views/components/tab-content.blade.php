@props([
    'name' => '',
])

@php
    $name = (string) $name;
@endphp

<div x-show="active === @js($name)"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="tab-content">
    {{ $slot }}
</div>
