<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center -pt-3 sm:pt-0 relative" style="background-image: url('{{ asset('img/background-login.jpg') }}'); background-size: cover; background-position: center;">
            <!-- Overlay para escurecer o fundo e destacar os elementos -->
            <div class="absolute inset-0 bg-black/50"></div>

            <div class="z-10 flex flex-col items-center">
                <a href="{{ route('login') }}">
                    <x-application-logo class="w-24 h-auto drop-shadow-2xl" />
                </a>
                <h1 class="m-4 text-2xl font-extrabold text-blue-500 uppercase tracking-widest" style="text-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8);">
                    Processos de Pagamento
                </h1>
            </div>

            <div class="z-10 w-full sm:max-w-md mt-6 px-8 py-10 bg-black/60 backdrop-blur-lg shadow-2xl overflow-hidden sm:rounded-2xl border border-white/10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
