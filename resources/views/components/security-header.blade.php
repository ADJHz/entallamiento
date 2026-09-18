@props(['actor'])

<header class="bg-[#650d31] px-5 py-2 text-white sm:px-8">
    <div class="mx-auto flex min-h-[58px] max-w-[1440px] items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold leading-tight sm:text-[23px]">Sistema de Registro de Tallas</h1>
            <p class="mt-1 text-[11px] text-[#e8dce2] sm:text-[13px]">Elementos de Seguridad</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] font-bold sm:text-xs">Seguridad</p>
                <p class="mt-1 text-[10px] text-[#e8dce2] sm:text-xs">Secretaría de Seguridad</p>
            </div>
            <x-access-account-menu :actor="$actor" />
        </div>
    </div>
</header>
