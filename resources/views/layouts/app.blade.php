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
    <body class="font-sans antialiased text-gray-800 bg-[#f1f5f9]">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="absolute left-0 top-0 z-50 flex h-screen w-72 flex-col overflow-y-hidden bg-orange-600 duration-300 ease-linear lg:static lg:translate-x-0 shadow-xl border-r border-orange-700">
                
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between gap-2 px-6 py-6 lg:py-6">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-full">
                        <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="w-16 h-16 rounded-2xl object-cover shadow-md border-2 border-orange-500">
                    </a>
                    <button @click="sidebarOpen = false" class="block lg:hidden text-orange-200 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Sidebar Menu -->
                <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
                    <nav class="mt-2 py-4 px-4 lg:mt-4 lg:px-6">
                        <div>
                            <h3 class="mb-4 ml-4 text-xs font-bold tracking-wider text-orange-200/80">MENU UTAMA</h3>
                            <ul class="mb-6 flex flex-col gap-1.5">
                                <li>
                                    <a href="{{ route('dashboard') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        Dashboard
                                    </a>
                                </li>
                                
                                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'cashier')
                                <li>
                                    <a href="{{ route('staff.cashier') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('staff.cashier') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('staff.cashier') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        Cashier
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        
                        @if (auth()->user()->role === 'admin')
                        <div>
                            <h3 class="mb-4 ml-4 text-xs font-bold tracking-wider text-orange-200/80">ADMINISTRATOR</h3>
                            <ul class="mb-6 flex flex-col gap-1.5">
                                <li>
                                    <a href="{{ route('admin.categories') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.categories') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.categories') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                        Kategori
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.menus') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.menus') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.menus') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                        Menu
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.qrcode') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.qrcode') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.qrcode') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        Kelola Meja
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.payments') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.payments') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.payments') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        Kelola Pembayaran
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.receipt-settings') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.receipt-settings') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.receipt-settings') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Setting Struk
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('profile') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('profile') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Profil
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.reports') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('admin.reports') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('admin.reports') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Laporan
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'marketing')
                        <div>
                            <h3 class="mb-4 ml-4 mt-6 text-xs font-bold tracking-wider text-orange-200/80">MARKETING</h3>
                            <ul class="mb-6 flex flex-col gap-1.5">
                                <li>
                                    <a href="{{ route('marketing.dashboard') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('marketing.dashboard') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('marketing.dashboard') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Dashboard Promo
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.promotions') }}" wire:navigate class="group relative flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-orange-50 duration-300 ease-in-out hover:bg-orange-700/80 hover:text-white {{ request()->routeIs('marketing.promotions') ? 'bg-orange-700 text-white shadow-inner' : '' }}">
                                        <svg class="w-5 h-5 {{ request()->routeIs('marketing.promotions') ? 'text-white' : 'text-orange-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Manajemen Promo
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
                
                <!-- Header (using navigation component) -->
                <livewire:layout.navigation />
                
                <!-- Main Content -->
                <main>
                    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                        {{ $slot }}
                    </div>
                </main>
                
            </div>
        </div>        <script>
            window.compressImage = function(file, quality = 0.6, maxWidth = 1200) {
                return new Promise((resolve, reject) => {
                    if (!file.type.match(/image.*/)) {
                        resolve(file);
                        return;
                    }
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = event => {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            if (width > maxWidth) {
                                height = Math.round(height * maxWidth / width);
                                width = maxWidth;
                            }
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                try {
                                    const fileName = file.name || 'image.jpg';
                                    const newFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });
                                    resolve(newFile);
                                } catch(e) {
                                    reject(e);
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = error => reject(error);
                    };
                    reader.onerror = error => reject(error);
                });
            };
        </script>
    </body>
</html>
