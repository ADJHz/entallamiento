<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">
    </head>
    <body class="min-w-[320px] bg-white font-sans text-gray-900 antialiased">
        <x-security-header :actor="$actor ?? auth()->user()" />

        <main class="min-h-[calc(100vh-74px)] bg-white px-5 py-8 sm:px-6 sm:py-10">
            {{ $slot }}
        </main>

        @fluxScripts
    </body>
</html>
