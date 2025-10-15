{{--Verifica si hay un elemento en el arreglo breadcrumb--}}
@if (count ($breadcrumbs))
    {{--mb: Margin bottom--}}
    <nav class="mb-2 block">
        <ol class= "flex flex-wrap text-slate-700 text-sm">
            @foreach ($breadcrumbs as $item)
            {{--Centra los elementos li--}}
            <li class = "flex items-center">
                {{--Si no es el primer elemento, usa "/"--}}
                @unless($loop->first)
                {{-- p: padding. x: eje horizontal izquierda o derecha. 2: significa a la derecha--}}
                {{--Padding del eje x--}}
                    <spam class="px-2 text-gray-400">/</spam>
                @endunless

                @isset($item['href'])
                {{--Si existe href muéstralo--}}
                <a href="{{$item ['href']}}"
                class="opacity-60 hover: opacity-100 transition ">{{$item['name']}}</a>
                {{--Si no hay href--}}
                @else
                    {{$item['name']}}
                @endisset

            </li>
            @endforeach
        </ol>
            {{--El último item aparecería resaltado en negritas --}}
            @if (count($breadcrumbs)>1)
            {{--mt:margin--}}
            {{--heading y 6 es el tamaño--}}
            <h6 class= "font-bold mt-2">
                {{ end($breadmcrumbs)['name']}}
            </h6>
            @endif
    </nav>
    @endif
    