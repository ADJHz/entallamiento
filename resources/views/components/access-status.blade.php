@props([
    'isElemento' => false,
    'name',
])

<section class="mx-auto w-full max-w-3xl rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
    <p class="text-sm font-semibold uppercase tracking-wide text-[#650d31]">
        {{ $isElemento ? 'Acceso de elemento' : 'Acceso administrativo' }}
    </p>
    <h2 class="mt-2 text-3xl font-normal text-gray-900">Bienvenido, {{ $name }}</h2>
    <p class="mt-3 text-gray-600">
        {{ $isElemento ? 'Tu acceso fue validado correctamente.' : 'Tu sesión administrativa fue validada correctamente.' }}
    </p>
</section>
