<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
        <script src="https://unpkg.com/pdfobject@2.3.0/pdfobject.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .trix ul {
                list-style-type: disc;
                padding-left: 2.5rem;
            }

            .trix ol {
                list-style-type: decimal;
                padding-left: 2.5rem;
            }

            trix-editor{
                border: 1px solid #818793;
            }
        </style>
    </head>
    <body class="pt-16 font-sans antialiased text-white bg-custom-blue">
        <div class="fixed w-full h-full background-effect-gradient -left-1/2 -z-50"></div>
        <div class="fixed w-full h-full background-effect-gradient -right-1/2 -z-50"></div>
        <livewire:layout.navigation />
        <main class="container mx-auto my-10">
            {{ $slot }}
        </main>
        <section class="bg-black bg-opacity-30">
          <div class="container flex flex-col items-center justify-between gap-3 py-10 mx-auto lg:flex-row lg:items-stretch">
            <div class="flex flex-col items-start justify-between flex-grow w-full gap-6 md:flex-row md:justify-around lg:justify-between text-slate-400">
              <div class="flex flex-col gap-3">
                <h4 class="text-2xl font-semibold">Marketing</h4>
                <p href="" class="text-md">Business Consulting</p>
                <p href="" class="text-md">Social Media Marketing</p>
                <p href="" class="text-md">Google Ads</p>
                <p href="" class="text-md">SEO Optimization</p>
                <p href="" class="text-md">Political Outreach</p>
              </div>
              <div class="flex flex-col gap-3">
                <h4 class="text-2xl font-semibold">Creatives</h4>
                <p href="" class="text-md">Videography</p>
                <p href="" class="text-md">Website Development</p>
                <p href="" class="text-md">Graphic Design</p>
                <p class="text-md">Logo Development</p>
              </div>
              <div class="flex flex-col gap-3">
                <h4 class="text-2xl font-semibold">Other</h4>
                <p class="text-md">Solar Panels</p>
                <p class="text-md">Real Estate Consulting</p>
                <p class="text-md">Sales Training</p>
                <p class="text-md">Campaign USA</p>
              </div>
            </div>
            <div class="flex flex-col items-end justify-between w-full">
              <a href="#">
                <x-application-logo class="block text-white fill-current max-w-14 lg:h-20" />
              </a>
              <div class="">
                <p class="text-lg text-end font-bebas sm:text-2xl">INFO@LURTSEMACOMMUNICATIONS.COM</p>
                <p class="text-lg text-end font-bebas sm:text-2xl">6390 NORM DRIVE, ANCHORAGE, ALASKA 99507, UNITED STATES</p>                      </div>
            </div>
          </div>
        </section>
        <section class="bg-black">
          <div class="container flex flex-col items-center justify-between gap-6 py-10 mx-auto lg:flex-row lg:items-start">
            <div class="flex flex-col items-center justify-start gap-3 sm:flex-row sm:gap-5 sm:items-start">
              <p class="text-lg">All Rights Reserved {{ date('Y') }}</p>
              <a href="" class="text-lg">Terms</a>
              <a href="" class="text-lg">Privacy Policy</a>
            </div>
            <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">
              <div class="flex items-center justify-center gap-3">
                <a class="transition-all duration-200 ease-in-out hover:opacity-70" target="_blank" href="https://www.youtube.com/@LurtsemaCommunications"><img src="{{ asset('images/logo-socmed/logo-yt.png') }}" class="w-11" alt=""></a>
                <a class="transition-all duration-200 ease-in-out hover:opacity-70" target="_blank" href="https://www.facebook.com/lurtsemacommunications"><img src="{{ asset('images/logo-socmed/logo-fb.png') }}" class="w-11" alt=""></a>
                <a class="transition-all duration-200 ease-in-out hover:opacity-70" target="_blank" href="https://www.instagram.com/lurtsemacommunications/"><img src="{{ asset('images/logo-socmed/logo-ig.png') }}" class="w-11" alt=""></a>
                <a class="transition-all duration-200 ease-in-out hover:opacity-70" target="_blank" href="http://www.linkedin.com/company/lurtsemacommunications"><img src="{{ asset('images/logo-socmed/logo-li.png') }}" class="w-11" alt=""></a>
                <a class="transition-all duration-200 ease-in-out hover:opacity-70" target="_blank" href="https://lurtsemacommunications.com/">
                  <x-application-logo class="w-11" />
                </a>
              </div>
            </div>
          </div>
        </section>
    </body>
</html>
