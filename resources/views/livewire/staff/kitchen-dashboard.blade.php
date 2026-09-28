<div class="p-6" wire:poll.3s="loadOrders">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Kitchen Dashboard</h2>
        <div class="flex space-x-2 text-sm text-gray-500 items-center">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
            </span>
            <span>Realtime Live</span>
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
        @forelse($orders as $order)
            <div class="bg-white rounded-xl shadow-sm border border-orange-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-orange-100 flex justify-between items-center bg-orange-50">
                    <div>
                        <span class="text-xs text-orange-600 font-bold uppercase tracking-wider">Meja {{ $order->table->table_number }}</span>
                        <h3 class="font-bold text-lg text-gray-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
                    </div>
                    <div class="text-right">
                        @if($order->status === 'verified')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 uppercase">Pesanan Masuk</span>
                        @elseif($order->status === 'cooking')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-purple-100 text-purple-800 uppercase">Dimasak</span>
                        @elseif($order->status === 'ready')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 uppercase animate-pulse">Siap Disajikan</span>
                        @endif
                        <span class="text-[11px] font-semibold text-gray-500 flex items-center justify-end mt-1 space-x-1">
                            <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $order->created_at->diffForHumans() }}</span>
                        </span>
                    </div>
                </div>
                
                <div class="p-4 flex-grow">
                    <ul class="space-y-3">
                        @foreach($order->orderDetails as $detail)
                            <li class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="bg-orange-100 text-orange-800 font-bold rounded flex items-center justify-center w-8 h-8 flex-shrink-0">
                                    {{ $detail->quantity }}x
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block">{{ $detail->bundle_id ? $detail->bundle->name . ' (Paket)' : $detail->menu->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100 space-y-2">
                    @if($order->status === 'verified')
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="startCooking({{ $order->id }})" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-2 rounded-xl transition text-xs flex justify-center items-center space-x-1">
                                <span>Mulai Masak</span>
                            </button>
                            <button wire:click="markAsServed({{ $order->id }})" wire:confirm="Pesanan sudah selesai dan diantar ke Meja {{ $order->table->table_number }}?" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-2 rounded-xl transition text-xs flex justify-center items-center space-x-1">
                                <span>Sajikan ke Meja</span>
                            </button>
                        </div>
                    @elseif($order->status === 'cooking')
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="markAsReady({{ $order->id }})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-2 rounded-xl transition text-xs flex justify-center items-center space-x-1">
                                <span>Siap Saji</span>
                            </button>
                            <button wire:click="markAsServed({{ $order->id }})" wire:confirm="Pesanan sudah selesai dan diantar ke Meja {{ $order->table->table_number }}?" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-2 rounded-xl transition text-xs flex justify-center items-center space-x-1">
                                <span>Sajikan ke Meja</span>
                            </button>
                        </div>
                    @elseif($order->status === 'ready')
                        <button wire:click="markAsServed({{ $order->id }})" wire:confirm="Pesanan sudah diantar ke Meja {{ $order->table->table_number }}?" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold py-3 rounded-xl transition text-sm flex justify-center items-center space-x-2 shadow-md">
                            <svg width="18" height="18" style="width: 18px; height: 18px; min-width: 18px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Antar &amp; Sajikan ke Meja {{ $order->table->table_number }}</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-xl shadow-sm border border-gray-200" style="align-self: start; height: max-content;">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-xl font-medium text-gray-900 mb-1">Dapur Santai</h3>
                <p class="text-gray-500">Tidak ada pesanan yang perlu dimasak saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
