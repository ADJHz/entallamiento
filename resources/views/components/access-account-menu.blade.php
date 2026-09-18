@props(['actor'])

@php
    $name = $actor instanceof \App\Models\Elemento ? $actor->nombre : $actor->name;
    $initials = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button
        type="button"
        class="flex items-center gap-2 rounded-full border border-white/30 bg-white/10 p-1 pr-2 text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70"
        @click="open = !open"
        :aria-expanded="open"
        aria-label="Abrir menú de cuenta"
    >
        <span class="flex size-8 items-center justify-center rounded-full bg-white text-xs font-bold text-[#650d31]">{{ $initials ?: 'U' }}</span>
        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">expand_more</span>
    </button>

    <div x-cloak x-show="open" x-transition class="absolute right-0 z-30 mt-2 w-64 rounded-lg border border-gray-200 bg-white p-2 text-gray-900 shadow-lg">
        <div class="border-b border-gray-100 px-3 py-2">
            <p class="truncate text-sm font-semibold">{{ $name }}</p>
            <p class="text-xs text-gray-500">{{ $actor instanceof \App\Models\Elemento ? 'Elemento de seguridad' : 'Administrador' }}</p>
        </div>

        <a href="{{ route('account.settings') }}" class="mt-1 flex items-center gap-3 rounded-md px-3 py-2 text-sm hover:bg-gray-100">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">settings</span>
            <span>Configuración</span>
        </a>

        <form method="POST" action="{{ $actor instanceof \App\Models\Elemento ? route('elementos.logout') : route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm text-[#650d31] hover:bg-[#f8edf1]">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">logout</span>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</div>
