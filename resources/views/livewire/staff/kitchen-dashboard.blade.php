<div class="p-6">
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
                    <span class="text-xs font-semibold text-gray-500 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $order->created_at->diffForHumans() }}</span>
                    </span>
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

                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    <button wire:click="markAsReady({{ $order->id }})" wire:confirm="Pesanan sudah siap dihidangkan?" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition text-sm flex justify-center items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Tandai Selesai Dimasak</span>
                    </button>
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
