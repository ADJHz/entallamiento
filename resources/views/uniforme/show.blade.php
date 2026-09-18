@php
    $decodeId = static function ($value): ?int {
        if (blank($value)) {
            return null;
        }

        try {
            return (int) \Illuminate\Support\Facades\Crypt::decryptString((string) $value);
        } catch (\Throwable) {
            return is_numeric($value) ? (int) $value : null;
        }
    };
    $direccionSeleccionada = $decodeId(old('direccion_id', $elemento->direccion_id));
    $colorFranjaSeleccionado = $decodeId(old('color_franja_id', $elemento->color_franja_id));
    $direccionSeleccionadaToken = filled($direccionSeleccionada)
        ? \Illuminate\Support\Facades\Crypt::encryptString((string) $direccionSeleccionada)
        : '';
    $colorFranjaSeleccionadoToken = filled($colorFranjaSeleccionado)
        ? \Illuminate\Support\Facades\Crypt::encryptString((string) $colorFranjaSeleccionado)
        : '';
    $tallasGuardadas = collect([
        $elemento->talla_chamarra,
        $elemento->talla_camisa,
        $elemento->talla_pantalon,
        $elemento->talla_cinturon,
        $elemento->talla_botas,
    ])
        ->filter(fn($talla) => filled($talla))
        ->isNotEmpty();
    $seleccionBloqueada = (bool) $tallasGuardadas;
@endphp

<x-layouts::access :actor="$elemento">
    <div class="mx-auto w-full max-w-3xl">
        <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#650d31]">Asistente de uniforme</p>
            <h2 class="mt-2 text-3xl font-normal text-gray-900">Hola, {{ $elemento->nombre }}</h2>
            <p class="mt-3 text-gray-600">
                @if ($seleccionBloqueada)
                    Ya tienes tus tallas guardadas. Se cargó la información registrada para ti.
                @else
                    Selecciona tu dirección. Color, franja de tu uniforme y captura tus tallas.
                @endif
            </p>

            @if (session('status'))
                <div class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="mt-4 rounded-md bg-red-50 px-4 py-3 text-sm text-[#b42b43]" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="uniforme-wizard" method="POST" action="{{ route('uniforme.store') }}" class="mt-6"
                data-opciones-url="{{ route('uniforme.opciones') }}"
                data-locked="{{ $seleccionBloqueada ? '1' : '0' }}">
                @csrf

                <input type="hidden" name="direccion_id" id="direccion_id_input" value="{{ $direccionSeleccionadaToken }}">
                <input type="hidden" name="color_franja_id" id="color_franja_id_input"
                    value="{{ $colorFranjaSeleccionadoToken }}">

                <div class="relative mb-6">
                    <label for="direccion_select_toggle" class="mb-1.5 block text-[13px] font-bold">Dirección <span
                            class="text-[#b42b43]">*</span></label>

                    <button id="direccion_select_toggle" type="button" data-dropdown-toggle="direccion_select_menu"
                        data-dropdown-placement="bottom-start" @if ($seleccionBloqueada) disabled @endif
                        class="inline-flex h-9 w-full items-center justify-between rounded-md border border-gray-300 bg-white px-2.5 text-left text-sm text-gray-700 shadow-xs transition hover:bg-gray-50 focus:border-[#a46880] focus:outline-none focus:ring-2 focus:ring-[#d6a5b6] @if ($seleccionBloqueada) cursor-not-allowed bg-gray-100 text-gray-500 @endif"
                        aria-expanded="false" aria-haspopup="true">
                        <span id="direccion_select_label">
                            {{ optional($direcciones->firstWhere('id', $direccionSeleccionada))->nombre ?? 'Selecciona tu dirección' }}
                        </span>
                        <svg class="h-3.5 w-3.5 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <div id="direccion_select_menu"
                        class="z-20 hidden w-full divide-y divide-gray-100 rounded-md border border-gray-200 bg-white shadow-lg"
                        role="menu" aria-labelledby="direccion_select_toggle">
                        <ul class="max-h-64 overflow-y-auto py-1 text-sm text-gray-700" aria-labelledby="direccion_select_toggle">
                            @foreach ($direcciones as $direccion)
                                <li>
                                    <button type="button"
                                        class="direccion-dropdown-option block w-full px-2.5 py-2 text-left hover:bg-[#fcf7f9] hover:text-[#650d31] focus:bg-[#fcf7f9] focus:outline-none"
                                        data-value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) $direccion->id) }}"
                                        data-label="{{ $direccion->nombre }}" role="menuitem">
                                        {{ $direccion->nombre }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="mb-6 text-center">
                    <h3 class="mb-2 text-sm font-bold text-gray-900">
                        Color y franja
                    </h3>

                    <p id="uniforme-opciones-message" class="mb-2 text-sm text-[#b42b43]" role="alert"
                        aria-live="polite">
                    </p>

                    <div class="flex w-full justify-center">
                        <div id="uniforme-opciones-grid"
                            class="grid w-full max-w-[24rem] grid-cols-1 gap-4 place-items-center sm:grid-cols-2 sm:max-w-[32rem]">
                            <p class="col-span-full text-sm text-gray-500">
                                Selecciona una dirección para ver las opciones disponibles.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold text-gray-900">Tallas</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="talla_chamarra" class="mb-1.5 block text-[13px] font-bold">Talla Chamarra <span
                                    class="text-[#b42b43]">*</span></label>
                            <input id="talla_chamarra" name="talla_chamarra" type="text" required
                                value="{{ old('talla_chamarra', $elemento->talla_chamarra) }}"
                                @if ($seleccionBloqueada) readonly @endif
                                class="block h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm text-gray-700 focus:border-[#a46880] focus:ring-[#a46880] @if ($seleccionBloqueada) bg-gray-100 text-gray-500 @endif">
                        </div>
                        <div>
                            <label for="talla_camisa" class="mb-1.5 block text-[13px] font-bold">Talla Camisa <span
                                    class="text-[#b42b43]">*</span></label>
                            <input id="talla_camisa" name="talla_camisa" type="text" required
                                value="{{ old('talla_camisa', $elemento->talla_camisa) }}"
                                @if ($seleccionBloqueada) readonly @endif
                                class="block h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm text-gray-700 focus:border-[#a46880] focus:ring-[#a46880] @if ($seleccionBloqueada) bg-gray-100 text-gray-500 @endif">
                        </div>
                        <div>
                            <label for="talla_pantalon" class="mb-1.5 block text-[13px] font-bold">Talla Pantalon <span
                                    class="text-[#b42b43]">*</span></label>
                            <input id="talla_pantalon" name="talla_pantalon" type="text" required
                                value="{{ old('talla_pantalon', $elemento->talla_pantalon) }}"
                                @if ($seleccionBloqueada) readonly @endif
                                class="block h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm text-gray-700 focus:border-[#a46880] focus:ring-[#a46880] @if ($seleccionBloqueada) bg-gray-100 text-gray-500 @endif">
                        </div>
                        <div>
                            <label for="talla_cinturon" class="mb-1.5 block text-[13px] font-bold">Talla Cinturon <span
                                    class="text-[#b42b43]">*</span></label>
                            <input id="talla_cinturon" name="talla_cinturon" type="text" required
                                value="{{ old('talla_cinturon', $elemento->talla_cinturon) }}"
                                @if ($seleccionBloqueada) readonly @endif
                                class="block h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm text-gray-700 focus:border-[#a46880] focus:ring-[#a46880] @if ($seleccionBloqueada) bg-gray-100 text-gray-500 @endif">
                        </div>
                        <div>
                            <label for="talla_botas" class="mb-1.5 block text-[13px] font-bold">Talla Botas <span
                                    class="text-[#b42b43]">*</span></label>
                            <input id="talla_botas" name="talla_botas" type="text" required
                                value="{{ old('talla_botas', $elemento->talla_botas) }}"
                                @if ($seleccionBloqueada) readonly @endif
                                class="block h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm text-gray-700 focus:border-[#a46880] focus:ring-[#a46880] @if ($seleccionBloqueada) bg-gray-100 text-gray-500 @endif">
                        </div>
                    </div>
                </div>

                <button id="uniforme-submit-button" type="submit" @if ($seleccionBloqueada) disabled @endif
                    class="mt-6 h-[38px] w-full rounded bg-[#650d31] text-sm font-bold text-white hover:bg-[#4f0a26] focus:outline-none focus:ring-2 focus:ring-[#a46880] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">{{ $seleccionBloqueada ? 'Información guardada' : 'Guardar uniforme' }}</button>
            </form>
        </div>
    </div>
</x-layouts::access>

@vite(['resources/js/uniforme.js'])
