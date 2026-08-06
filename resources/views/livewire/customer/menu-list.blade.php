@php
    $currentTheme = $activeEvent->theme ?? 'general';
    $theme = [
        'valentine' => [
            'headerBg' => 'bg-gradient-to-r from-pink-500 via-red-500 to-rose-600 text-white',
            'primary' => 'bg-pink-500',
            'primaryHover' => 'hover:bg-pink-600',
            'textHover' => 'hover:text-pink-600',
            'primaryGradient' => 'from-pink-500 to-pink-600',
            'primaryHoverGradient' => 'hover:from-pink-600 hover:to-pink-700',
            'text' => 'text-pink-600',
            'textDark' => 'text-pink-700',
            'textLight' => 'text-pink-300',
            'bgLight' => 'bg-pink-50',
            'bgLighter' => 'bg-pink-100/50',
            'border' => 'border-pink-200',
            'borderLight' => 'border-pink-100',
            'ring' => 'focus:border-pink-500 focus:ring-pink-200',
            'bestSeller' => 'from-pink-500 to-rose-500',
            'icon' => '💖',
        ],
        'kemerdekaan' => [
            'headerBg' => 'bg-gradient-to-r from-red-600 to-white text-gray-900 border-b-4 border-red-600',
            'primary' => 'bg-red-600',
            'primaryHover' => 'hover:bg-red-700',
            'textHover' => 'hover:text-red-600',
            'primaryGradient' => 'from-red-600 to-red-700',
            'primaryHoverGradient' => 'hover:from-red-700 hover:to-red-800',
            'text' => 'text-red-600',
            'textDark' => 'text-red-700',
            'textLight' => 'text-red-300',
            'bgLight' => 'bg-red-50',
            'bgLighter' => 'bg-red-100/50',
            'border' => 'border-red-200',
            'borderLight' => 'border-red-100',
            'ring' => 'focus:border-red-600 focus:ring-red-200',
            'bestSeller' => 'from-red-600 to-red-800',
            'icon' => '🇮🇩',
        ],
        'natal' => [
            'headerBg' => 'bg-gradient-to-r from-emerald-700 via-green-600 to-red-600 text-white',
            'primary' => 'bg-emerald-600',
            'primaryHover' => 'hover:bg-emerald-700',
            'textHover' => 'hover:text-emerald-600',
            'primaryGradient' => 'from-emerald-600 to-emerald-700',
            'primaryHoverGradient' => 'hover:from-emerald-700 hover:to-emerald-800',
            'text' => 'text-emerald-600',
            'textDark' => 'text-emerald-700',
            'textLight' => 'text-emerald-300',
            'bgLight' => 'bg-emerald-50',
            'bgLighter' => 'bg-emerald-100/50',
            'border' => 'border-emerald-200',
            'borderLight' => 'border-emerald-100',
            'ring' => 'focus:border-emerald-600 focus:ring-emerald-200',
            'bestSeller' => 'from-emerald-600 to-red-600',
            'icon' => '🎄',
        ],
        'general' => [
            'headerBg' => 'bg-gradient-to-r from-orange-500 to-amber-500 text-white',
            'primary' => 'bg-orange-500',
            'primaryHover' => 'hover:bg-orange-600',
            'textHover' => 'hover:text-orange-600',
            'primaryGradient' => 'from-orange-500 to-orange-600',
            'primaryHoverGradient' => 'hover:from-orange-600 hover:to-orange-700',
            'text' => 'text-orange-600',
            'textDark' => 'text-orange-700',
            'textLight' => 'text-orange-300',
            'bgLight' => 'bg-orange-50',
            'bgLighter' => 'bg-orange-100/50',
            'border' => 'border-orange-200',
            'borderLight' => 'border-orange-100',
            'ring' => 'focus:border-orange-500 focus:ring-orange-200',
            'bestSeller' => 'from-orange-500 to-red-500',
            'icon' => '✨',
        ],
    ];
    $t = $theme[$currentTheme] ?? $theme['general'];
@endphp

<div class="w-full pt-6 pb-28 relative" x-data="{ 
    showHookModal: false,
    init() {
        // Cek apakah sudah pernah melihat popup di sesi/meja ini
        const table = '{{ $table_number ?? 'general' }}';
        const modalKey = 'rumpo_hook_seen_' + table;
        if (!sessionStorage.getItem(modalKey)) {
            this.showHookModal = true;
        }
    },
    dismissModal() {
        const table = '{{ $table_number ?? 'general' }}';
        sessionStorage.setItem('rumpo_hook_seen_' + table, 'true');
        this.showHookModal = false;
    }
}">

    <!-- Pop-up Headline / Hook Interaktif QR Code Scan -->
    <div x-show="showHookModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-md flex items-center justify-center p-4" 
         style="display: none;">
        
        <div class="relative bg-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl border border-white/20 transform">
            <!-- Banner & Header -->
            <div class="{{ $t['headerBg'] }} p-6 text-center relative overflow-hidden">
                <!-- Decorative Elements -->
                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -left-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                
                @if($table_number)
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-2 text-white">
                        📍 Meja Nomor {{ $table_number }}
                    </span>
                @endif

                @if($activeEvent)
                    <div class="text-3xl mb-1">
                        {{ $t['icon'] }}
                    </div>
                    <h3 class="text-xl font-extrabold tracking-tight mb-1">{{ $activeEvent->title }}</h3>
                    @if($activeEvent->headline)
                        <p class="text-xs font-medium opacity-90 leading-snug">{{ $activeEvent->headline }}</p>
                    @endif
                @else
                    <div class="text-3xl mb-1">☕</div>
                    <h3 class="text-2xl font-black tracking-tight">Selamat Datang!</h3>
                    <p class="text-xs font-medium opacity-90 mt-1">Nikmati hidangan spesial &amp; suasana hangat di Rumpo Cafe</p>
                @endif
            </div>

            <!-- Content Body -->
            <div class="p-6 text-center">
                @if($activeEvent && $activeEvent->banner_image)
                    <div class="mb-4 rounded-2xl overflow-hidden shadow-md border border-gray-100">
                        <img src="{{ Storage::url($activeEvent->banner_image) }}" class="w-full h-40 object-cover">
                    </div>
                @endif

                @if($activeEvent && $activeEvent->description)
                    <p class="text-xs text-gray-600 mb-4 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">
                        {{ $activeEvent->description }}
                    </p>
                @else
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                        Kami siap melayani pesanan Anda secara langsung dari meja. Pilihlah menu favorit Anda dan nikmati pelayanan terbaik dari kami!
                    </p>
                @endif

                @if($activeEvent && $activeEvent->coupon_code)
                    <div class="mb-5 p-3 {{ $t['bgLight'] }} rounded-xl border {{ $t['border'] }}">
                        <span class="text-[11px] {{ $t['text'] }} font-bold uppercase block mb-0.5">Kode Diskon Spesial</span>
                        <div class="font-mono font-black {{ $t['textDark'] }} text-lg tracking-wider">{{ $activeEvent->coupon_code }}</div>
                        @if($activeEvent->discount_percentage > 0)
                            <span class="text-[10px] text-green-600 font-bold">Potongan {{ $activeEvent->discount_percentage }}%</span>
                        @endif
                    </div>
                @endif

                <button @click="dismissModal()" class="w-full bg-gradient-to-r {{ $t['primaryGradient'] }} {{ $t['primaryHoverGradient'] }} text-white font-bold py-3 px-6 rounded-2xl transition shadow-lg active:scale-95 flex items-center justify-center space-x-2">
                    <span>Lihat Menu &amp; Pesan Sekarang</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
    
    <div class="px-4">
        
        <!-- Header & Cart Icon -->
        <div class="flex justify-between items-center mb-6 sticky top-0 bg-white/95 backdrop-blur-md pt-4 pb-3 z-30 shadow-sm -mx-4 px-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Menu Kami</h1>
                <p class="text-sm text-gray-500">Silakan pilih pesanan Anda</p>
            </div>
            <a href="{{ route('customer.cart') }}" class="relative p-2.5 {{ $t['bgLight'] }} rounded-full shadow-sm hover:shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $t['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @if($cartCount > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-full shadow-lg z-50 text-sm flex items-center space-x-2 animate-bounce w-[90%] justify-center">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-2 rounded-full shadow-lg z-50 text-sm flex items-center space-x-2 animate-bounce w-[90%] justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Categories -->
        <div class="mb-6 flex overflow-x-auto whitespace-nowrap pb-2 scrollbar-hide -mx-4 px-4 snap-x">
            <button wire:click="selectCategory(null)" class="snap-start inline-block px-5 py-2 rounded-full text-sm font-semibold transition {{ $selectedCategory === null ? $t['primary'].' text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                Semua
            </button>
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})" class="snap-start inline-block px-5 py-2 ml-2 rounded-full text-sm font-semibold transition {{ $selectedCategory === $category->id ? $t['primary'].' text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <!-- Bundles Section -->
        @if(!$selectedCategory && $bundles->count() > 0)
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-6 h-6 {{ $t['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <h2 class="text-xl font-bold text-gray-800">Paket Hemat</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($bundles as $bundle)
                    <div wire:click="openBundleDetail({{ $bundle->id }})" class="cursor-pointer bg-gradient-to-br from-white to-white rounded-3xl shadow-[0_2px_10px_rgb(0,0,0,0.06)] border {{ $t['borderLight'] }} overflow-hidden flex flex-row sm:flex-col items-stretch transform transition hover:shadow-md active:scale-[0.98]">
                        <div class="relative w-1/3 sm:w-full h-32 sm:h-40 flex-shrink-0">
                            @if($bundle->image)
                                <img src="{{ Storage::url($bundle->image) }}" alt="{{ $bundle->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full {{ $t['bgLighter'] }} flex items-center justify-center {{ $t['textLight'] }}">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 left-2 {{ $t['primary'] }} text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-md flex items-center space-x-1 z-10">
                                <span>PROMO PAKET</span>
                            </div>
                        </div>
                        <div class="p-4 flex flex-col flex-grow justify-between w-2/3 sm:w-full">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">{{ $bundle->name }}</h3>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $bundle->description }}</p>
                            </div>
                            <div class="flex justify-between items-center w-full">
                                <span class="font-extrabold {{ $t['text'] }} text-[15px]">Rp {{ number_format($bundle->price, 0, ',', '.') }}</span>
                                <div class="p-2 {{ $t['primary'] }} text-white rounded-full flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">Semua Menu</h2>
        </div>
        @endif

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($menus as $menu)
                <div wire:click="openDetail({{ $menu->id }})" class="cursor-pointer bg-white rounded-3xl shadow-[0_2px_10px_rgb(0,0,0,0.06)] border border-gray-100/50 overflow-hidden flex flex-row sm:flex-col items-stretch transform transition hover:shadow-md active:scale-[0.98]">
                    <div class="relative w-1/3 sm:w-full h-32 sm:h-40 flex-shrink-0">
                        @if($menu->image)
                            <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        @if($menu->is_best_seller)
                            <div class="absolute top-2 left-2 bg-gradient-to-r {{ $t['bestSeller'] }} text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-md flex items-center space-x-1 z-10">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span>Best Seller</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-grow justify-between w-2/3 sm:w-full">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">{{ $menu->name }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $menu->description }}</p>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <span class="font-extrabold {{ $t['text'] }} text-[15px]">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            <div class="p-2 {{ $t['bgLight'] }} {{ $t['text'] }} rounded-full flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 py-10 text-center flex flex-col items-center">
                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-gray-500 font-medium">Tidak ada menu di kategori ini.</p>
                </div>
            @endforelse
        </div>

    </div>

    @if($cartCount > 0)
    <!-- Floating Checkout Bar -->
    <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
        <div class="max-w-md mx-auto">
            <a href="{{ route('customer.cart') }}" class="w-full flex justify-between items-center {{ $t['primary'] }} text-white font-bold py-3 px-6 rounded-2xl shadow-lg {{ $t['primaryHover'] }} transition transform hover:scale-[1.02] active:scale-[0.98]">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>{{ $cartCount }} Item</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span>Lihat Keranjang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- Bottom Sheet Modal -->
    @if($showModal && ($selectedMenu || $selectedBundle))
    <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
        <!-- Backdrop -->
        <div wire:click="closeDetail" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] transition-transform transform translate-y-0" x-data x-init="gsap.from($el, {y: '100%', duration: 0.3, ease: 'power2.out'})">
            
            <!-- Close button -->
            <button wire:click="closeDetail" class="absolute top-4 right-4 z-10 p-2 bg-white/80 backdrop-blur-md text-gray-800 rounded-full shadow-sm hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Image Area -->
            <div class="w-full h-56 bg-gray-100 relative shrink-0">
                @php
                    $item = $isBundleModal ? $selectedBundle : $selectedMenu;
                @endphp
                @if($item->image)
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-contain p-4">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $item->name }}</h2>
                <p class="text-sm text-gray-600 mb-6 leading-relaxed">{{ $item->description }}</p>

                @if($isBundleModal && $item->items)
                    <div class="mb-4">
                        <h4 class="font-bold text-sm text-gray-800 mb-2">Isi Paket:</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600">
                            @foreach($item->items as $bundleItem)
                                <li>{{ $bundleItem->quantity }}x {{ $bundleItem->menu->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Khusus (Opsional)</label>
                        <textarea wire:model="notes" rows="3" class="w-full rounded-xl border-gray-200 shadow-sm {{ $t['ring'] }} transition text-sm p-3" placeholder="Contoh: Pedas sedikit, tanpa sayur..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Bar -->
            <div class="bg-white border-t border-gray-100 p-4 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.1)] flex items-center space-x-4 shrink-0 z-10">
                <!-- Quantity Controls -->
                <div class="flex items-center bg-gray-50 rounded-full border border-gray-200">
                    <button wire:click="decrementQuantity" class="w-10 h-10 flex items-center justify-center text-gray-600 {{ $t['textHover'] }} transition" {{ $quantity <= 1 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    </button>
                    <span class="w-10 text-center font-bold text-gray-900">{{ $quantity }}</span>
                    <button wire:click="incrementQuantity" class="w-10 h-10 flex items-center justify-center text-gray-600 {{ $t['textHover'] }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
                
                <!-- Add Button -->
                <button wire:click="addToCart" class="flex-grow {{ $t['primary'] }} text-white font-bold py-3 px-4 rounded-full shadow-lg {{ $t['primaryHover'] }} transition transform active:scale-95 flex justify-between items-center">
                    <span>Tambah</span>
                    <span>Rp {{ number_format($item->price * $quantity, 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
