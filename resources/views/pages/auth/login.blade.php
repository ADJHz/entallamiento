<x-layouts::auth.login :title="__('Log in')">
    <h2 class="text-[27px] font-normal leading-tight">Bienvenido</h2>
    <h3 class="mt-1 text-base font-bold leading-tight sm:text-[17px]">Acceso Por Clave Servidor Público y CUIP</h3>

    @if ($teamInvitation ?? null)
        <div class="mt-4">
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Log in')" />
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mt-4 text-sm text-[#b42b43]" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-7">
        @csrf

        <div class="mb-3.5">
            <label for="email" class="mb-1.5 block text-[13px] font-bold">Clave de Servidor Público <span class="text-[#b42b43]">*</span></label>
            <input id="email" name="email" type="text" value="{{ old('email') }}" placeholder="Ingrese su clave de servidor público" required autofocus autocomplete="username" class="block h-8 w-full rounded-md border border-gray-300 bg-white px-2.5 text-[13px] text-gray-600 placeholder-gray-500 focus:border-[#a46880] focus:ring-[#a46880]">
        </div>

        <div class="relative mb-3.5">
            <label for="identification_type_toggle" class="mb-1.5 block text-[13px] font-bold">Tipo de Identificación</label>
            <input id="identification_type" name="identification_type" type="hidden" value="cuip">
            <button id="identification_type_toggle" data-dropdown-toggle="identification_type_menu" data-dropdown-placement="bottom-start" type="button" class="inline-flex h-9 w-full items-center justify-between rounded-md border border-gray-300 bg-white px-2.5 text-left text-[13px] text-gray-600 shadow-xs hover:bg-gray-50 focus:border-[#a46880] focus:outline-none focus:ring-2 focus:ring-[#d6a5b6]" aria-expanded="false" aria-haspopup="true">
                <span id="identification_type_label">CUIP</span>
                <svg class="h-3 w-3 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <div id="identification_type_menu" class="z-20 hidden w-full divide-y divide-gray-100 rounded-md border border-gray-200 bg-white shadow-lg" role="menu">
                <ul class="py-1 text-[13px] text-gray-700" aria-labelledby="identification_type_toggle">
                    <li>
                        <button type="button" class="identification-dropdown-option block w-full px-2.5 py-2 text-left hover:bg-[#fcf7f9] hover:text-[#650d31] focus:bg-[#fcf7f9] focus:outline-none" data-value="cuip" data-label="CUIP" role="menuitem">CUIP</button>
                    </li>
                    <li>
                        <button type="button" class="identification-dropdown-option block w-full px-2.5 py-2 text-left hover:bg-[#fcf7f9] hover:text-[#650d31] focus:bg-[#fcf7f9] focus:outline-none" data-value="curp" data-label="CURP" role="menuitem">CURP</button>
                    </li>
                </ul>
            </div>
            <span class="mt-0.5 block text-[11px] font-bold text-[#60728b]">Utiliza el CURP sólo si no tienes CUIP</span>
        </div>

        <div class="mb-3.5">
            <label id="identification_field_label" for="password" class="mb-1.5 block text-[13px] font-bold"><span id="identification_field_label_text">CUIP</span> <span class="text-[#b42b43]">*</span></label>
            <div class="flex">
                <input id="password" name="password" type="password" placeholder="Ingrese su CUIP" required autocomplete="current-password" class="block h-8 min-w-0 flex-1 rounded-s-md border border-gray-300 bg-white px-2.5 text-[13px] text-gray-600 placeholder-gray-500 focus:z-10 focus:border-[#a46880] focus:ring-[#a46880]">
                <button type="button" class="h-8 w-[62px] rounded-e-md border border-l-0 border-[#a46880] bg-white text-[13px] text-[#a46880] hover:bg-[#fcf7f9] focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#d6a5b6]">Buscar</button>
            </div>
        </div>

        <div class="mb-3.5">
            <label for="element_name" class="mb-1.5 block text-[13px] font-bold">Nombre del Elemento</label>
            <input id="element_name" type="text" readonly class="block h-8 w-full rounded-md border border-gray-300 bg-[#e9edf1] px-2.5 text-[13px] text-gray-600">
        </div>

        <hr class="my-[18px] border-gray-300">

        <button type="submit" class="h-[34px] w-full rounded bg-[#a46880] text-[13px] font-bold text-white hover:bg-[#925970] focus:outline-none focus:ring-2 focus:ring-[#d6a5b6] focus:ring-offset-2" data-test="login-button">Ingresar al Sistema</button>
        <a class="mt-[52px] block text-center text-[13px] text-[#006dff] underline hover:text-blue-800" href="{{ route('admin.login') }}">Acceder con usuario y contraseña</a>
    </form>
</x-layouts::auth.login>
