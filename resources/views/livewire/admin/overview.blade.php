<div class="space-y-6">
        
        <!-- Welcome Banner -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
                <div class="text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-gray-500 text-sm sm:text-base">Pantau aktivitas Rumpo Cafe hari ini. Pastikan semua hidangan dan pelayanan berjalan dengan lancar.</p>
                </div>
                <div class="flex flex-wrap justify-center sm:justify-end gap-3 w-full sm:w-auto">
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'cashier')
                    <a href="{{ route('staff.cashier') }}" wire:navigate class="bg-orange-500 hover:bg-orange-600 text-white transition-all duration-300 rounded-lg px-5 py-2.5 font-medium flex items-center space-x-2 shadow-sm whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Kasir</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
            
            <!-- Stat 1 -->
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center justify-center rounded-lg bg-orange-50 text-orange-500 w-12 h-12">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-500 text-right">Pendapatan</span>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-1 truncate">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </h4>
                    <p class="text-xs text-gray-400">Hari ini</p>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center justify-center rounded-lg bg-orange-50 text-orange-500 w-12 h-12">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-500 text-right">Pesanan</span>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-1">
                        {{ $todayOrders }}
                    </h4>
                    <p class="text-xs text-gray-400">Hari ini</p>
                </div>
            </div>

            <!-- Stat 4 -->
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center justify-center rounded-lg bg-orange-50 text-orange-500 w-12 h-12">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-500 text-right">Menu</span>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-1">
                        {{ $totalMenus }}
                    </h4>
                    <p class="text-xs text-gray-400">Total Menu</p>
                </div>
            </div>
            
        </div>
</div>
