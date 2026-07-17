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
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex">
            <!-- Left Side: Image/Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-orange-500 to-orange-700 items-center justify-center relative overflow-hidden">
                <!-- Decorative Circles -->
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-black opacity-20 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
                
                <div class="relative z-10 text-center px-12 text-white">
                    <div class="flex justify-center mb-8">
                        <div class="bg-white/20 p-2 rounded-3xl backdrop-blur-md border border-white/30 shadow-2xl overflow-hidden">
                            <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="w-24 h-24 object-cover rounded-2xl shadow-inner">
                        </div>
                    </div>
                    <h1 class="text-5xl font-extrabold tracking-tight mb-4 drop-shadow-md">Rumpo Cafe</h1>
                    <p class="text-xl text-orange-100 font-medium">Sistem Pemesanan Mandiri Berbasis QR</p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 relative">
                <!-- Decorative elements for mobile -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-orange-100 rounded-full blur-3xl lg:hidden opacity-50"></div>
                
                <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 relative z-10">
                    <div class="lg:hidden text-center mb-8">
                        <div class="inline-flex rounded-2xl mb-4 overflow-hidden border border-gray-100 shadow-sm">
                            <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="w-14 h-14 object-cover">
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Rumpo Cafe</h2>
                    </div>
                    
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
