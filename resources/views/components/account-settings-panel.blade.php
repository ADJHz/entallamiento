@props(['actor'])

@php
    $isElemento = $actor instanceof \App\Models\Elemento;
    $name = $isElemento ? $actor->nombre : $actor->name;
@endphp

<section class="mx-auto w-full max-w-3xl rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
    <div class="flex items-start gap-4">
        <span class="material-symbols-outlined mt-1 text-3xl text-[#650d31]" aria-hidden="true">settings</span>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[#650d31]">Configuración</p>
            <h1 class="mt-2 text-2xl font-normal text-gray-900">{{ $name }}</h1>
            <p class="mt-2 text-gray-600">
                {{ $isElemento ? 'Configuración de acceso del elemento.' : 'Configuración de la cuenta administrativa.' }}
            </p>
        </div>
    </div>
</section>
