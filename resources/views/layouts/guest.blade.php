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
    <body class="font-sans antialiased text-white bg-custom-blue">
        <div class="fixed w-full h-full background-effect-gradient -left-1/2 -z-10"></div>
        <div class="fixed w-full h-full background-effect-gradient -right-1/2 -z-10"></div>
        <div class="flex flex-col items-center min-h-screen pt-16 sm:justify-center sm:pt-0">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 text-white fill-current" />
                </a>
            </div>
            
            <div class="container w-full px-6 py-4 mt-6 overflow-hidden shadow-md sm:rounded-lg sm:max-w-lg">
                <h1 class="text-4xl font-extrabold text-center text-white">Lurtsema Communications Client Portal</h1>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
