<div class="p-6">
    <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center mb-6 gap-4">
        <div class="flex items-center gap-3 w-full xl:w-auto">
            <h2 class="text-2xl font-bold text-gray-900">Cashier Dashboard</h2>
            <div class="flex items-center" title="Realtime Live">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">
            <div class="flex rounded-lg overflow-hidden border border-gray-300 bg-white shadow-sm shrink-0 w-full lg:w-96">
                <button wire:click="$set('tab', 'active')" class="flex-1 px-4 py-2 text-sm font-medium transition {{ $tab === 'active' ? 'bg-orange-500 text-white' : 'text-gray-700 hover:bg-gray-50' }}">Belum Diproses</button>
                <button wire:click="$set('tab', 'completed')" class="flex-1 px-4 py-2 text-sm font-medium transition border-l border-gray-200 {{ $tab === 'completed' ? 'bg-orange-500 text-white' : 'text-gray-700 hover:bg-gray-50' }}">Riwayat Selesai</button>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 w-full flex-grow">
                <!-- Filter Section -->
                <div class="w-full sm:w-auto shrink-0 flex gap-2">
                    <select wire:model.live="dateFilter" class="w-full sm:w-40 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 pl-3 pr-8 text-left">
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="all">Keseluruhan</option>
                        <option value="custom">Pilih Tanggal</option>
                    </select>
                    
                    @if($dateFilter === 'custom')
                        <input type="date" wire:model.live="customDate" class="w-full sm:w-40 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3">
                    @endif
                </div>

                <!-- Search Section -->
                <div class="relative w-full flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari nama, hp, pesanan..." class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2">
                </div>
            </div>
        </div>
    </div>

    <!-- Audio untuk notifikasi -->
    <audio id="notificationSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('play-notification', () => {
                let audio = document.getElementById('notificationSound');
                audio.play().catch(e => console.log('Audio autoplay prevented:', e));
            });
        });
    </script>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($this->orders as $order)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Meja {{ $order->table->table_number }}</span>
                        <h3 class="font-bold text-lg text-gray-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
                    </div>
                    @php
                        $statusColors = [
                            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
                            'waiting_verification' => 'bg-orange-100 text-orange-800',
                            'verified' => 'bg-blue-100 text-blue-800',
                            'cooking' => 'bg-purple-100 text-purple-800',
                            'ready' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                        {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                
                <div class="p-4 flex-grow space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2 text-sm">
                        <span class="text-gray-500">Pemesan</span>
                        <div class="text-right">
                            <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500 flex items-center justify-end mt-0.5 space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>{{ $order->customer_phone }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-2 block">Item Pesanan</span>
                        <ul class="space-y-2 text-sm text-gray-700">
                            @foreach($order->orderDetails as $detail)
                                <li class="flex justify-between">
                                    <span>{{ $detail->quantity }}x {{ $detail->bundle_id ? $detail->bundle->name . ' (Paket)' : $detail->menu->name }}</span>
                                    <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="flex justify-between items-center border-t border-gray-100 pt-2 font-bold text-gray-900">
                        <span>Total Tagihan</span>
                        <span class="text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>

                    @if($order->payment && $order->payment->status === 'pending')
                        <div class="border-t border-gray-100 pt-3">
                            <span class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-2 block">Bukti Pembayaran</span>
                            <div class="rounded-lg text-center relative cursor-pointer" x-data="{ open: false }">
                                <img src="{{ Storage::url($order->payment->proof_image) }}" alt="Bukti" class="w-full h-auto object-contain rounded-xl shadow-sm border border-gray-200" @click="open = true">
                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 p-4" style="display: none;">
                                    <div class="relative max-w-3xl w-full bg-white rounded-lg overflow-hidden" @click.away="open = false">
                                        <button @click="open = false" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 z-10">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                        <img src="{{ Storage::url($order->payment->proof_image) }}" alt="Bukti Pembayaran" class="w-full h-auto max-h-[80vh] object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    @if($order->status === 'waiting_verification' && $order->payment && $order->payment->status === 'pending')
                        <div class="flex space-x-2">
                            <button wire:click="verifyPayment({{ $order->id }}, 'verified')" wire:confirm="Konfirmasi pembayaran VALID?" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition text-sm text-center">
                                Valid (Terima)
                            </button>
                            <button wire:click="verifyPayment({{ $order->id }}, 'rejected')" wire:confirm="Tolak pembayaran ini?" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition text-sm text-center">
                                Invalid (Tolak)
                            </button>
                        </div>
                    @elseif($order->status === 'waiting_payment')
                        <div class="text-center py-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu pelanggan mengunggah ulang bukti pembayaran...</span>
                        </div>
                    @elseif(in_array($order->status, ['verified', 'cooking', 'ready']))
                        <div class="flex space-x-2">
                            <a href="{{ route('order.print', $order->id) }}" target="_blank" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 rounded-lg transition text-sm flex justify-center items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Struk</span>
                            </a>
                            
                            @if($order->status === 'verified')
                                <button wire:click="updateOrderStatus({{ $order->id }}, 'cooking')" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 rounded-lg transition text-sm text-center">
                                    Mulai Masak
                                </button>
                            @elseif($order->status === 'cooking')
                                <button wire:click="updateOrderStatus({{ $order->id }}, 'ready')" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition text-sm text-center">
                                    Siap Saji
                                </button>
                            @elseif($order->status === 'ready')
                                <button wire:click="completeOrder({{ $order->id }})" wire:confirm="Tandai pesanan telah selesai disajikan?" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition text-sm text-center">
                                    Selesai
                                </button>
                            @endif
                        </div>
                    @elseif($order->status === 'completed')
                        <a href="{{ route('order.print', $order->id) }}" target="_blank" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 rounded-lg transition text-sm flex justify-center items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Cetak Struk</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-xl shadow-sm border border-gray-200" style="align-self: start; height: max-content;">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900">Belum ada pesanan</h3>
                <p class="text-gray-500">Pesanan baru akan muncul di sini secara otomatis.</p>
            </div>
        @endforelse
    </div>
</div>
