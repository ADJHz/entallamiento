<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-w-[320px] bg-white font-sans text-gray-900 antialiased">
        <header class="bg-[#650d31] px-5 py-2 text-white sm:px-8">
            <div class="mx-auto flex min-h-[58px] max-w-[1440px] items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-bold leading-tight sm:text-[23px]">Sistema de Registro de Tallas</h1>
                    <p class="mt-1 text-[11px] text-[#e8dce2] sm:text-[13px]">Elementos de Seguridad</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold sm:text-xs">Seguridad</p>
                    <p class="mt-1 text-[10px] text-[#e8dce2] sm:text-xs">Secretaría de Seguridad</p>
                </div>
            </div>
        </header>

        <main class="flex justify-center px-5 pb-6 pt-8 sm:px-6 sm:pt-9">
            <section class="w-full max-w-[382px]">
                {{ $slot }}
            </section>
        </main>

        @fluxScripts
    </body>
</html>
