<x-layouts::auth.login :title="__('Admin login')">
    <h2 class="text-[17px] font-bold leading-tight">Acceso con usuario y contraseña</h2>

    @if (isset($errors) && $errors->any())
        <div class="mt-5 text-sm text-[#b42b43]" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-7">
        @csrf

        <div class="mb-3.5">
            <label for="admin_email" class="mb-1.5 block text-[13px] font-bold">Usuario <span class="text-[#b42b43]">*</span></label>
            <input id="admin_email" name="email" type="text" value="{{ old('email') }}" placeholder="Ingrese su usuario" required autofocus autocomplete="username" class="block h-8 w-full rounded-md border border-gray-300 bg-white px-2.5 text-[13px] text-gray-600 placeholder-gray-500 focus:border-[#a46880] focus:ring-[#a46880]">
        </div>

        <div class="mb-3.5">
            <label for="admin_password" class="mb-1.5 block text-[13px] font-bold">Contraseña <span class="text-[#b42b43]">*</span></label>
            <input id="admin_password" name="password" type="password" placeholder="Ingrese su contraseña" required autocomplete="current-password" class="block h-8 w-full rounded-md border border-gray-300 bg-white px-2.5 text-[13px] text-gray-600 placeholder-gray-500 focus:border-[#a46880] focus:ring-[#a46880]">
        </div>

        <hr class="my-[18px] border-gray-300">

        <button type="submit" class="h-[34px] w-full rounded bg-[#a46880] text-[13px] font-bold text-white hover:bg-[#925970] focus:outline-none focus:ring-2 focus:ring-[#d6a5b6] focus:ring-offset-2" data-test="admin-login-button">Iniciar Sesión</button>
        <a class="mt-[52px] block text-center text-[13px] text-[#006dff] underline hover:text-blue-800" href="{{ route('login') }}">Acceder con Clave de Servidor Público</a>
    </form>
</x-layouts::auth.login>
