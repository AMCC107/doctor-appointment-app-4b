@props([
    'name' => '',
    'label' => '',
    'error' => false,
])

@php
    $name = (string) $name;
    $error = filter_var($error, FILTER_VALIDATE_BOOLEAN);
@endphp

<li class="me-2" role="presentation">
    <a href="#"
       role="tab"
       x-on:click.prevent="active = @js($name)"
       :aria-current="active === @js($name) ? 'page' : undefined"
       :class="{
           'text-red-600 border-red-600': @js($error),
           'text-blue-600 border-blue-600': !@js($error) && active === @js($name),
           'border-transparent hover:text-blue-600 hover:border-gray-300 text-gray-500': !@js($error) && active !== @js($name),
       }"
       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200">
        @if($error)
            <i class="fa-solid fa-circle-exclamation text-red-500 animate-pulse me-2" aria-hidden="true"></i>
        @endif
        @if($label !== '')
            {{ $label }}
        @else
            {{ $slot }}
        @endif
    </a>
</li>
