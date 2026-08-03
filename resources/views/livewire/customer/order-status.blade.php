<div class="min-h-screen bg-gray-50 pt-6 pb-24 relative">
    <div class="max-w-md mx-auto px-4">
        
        <!-- Header -->
        <div class="flex items-center mb-6 sticky top-0 bg-gray-50 bg-opacity-90 backdrop-blur-sm pt-4 pb-2 z-10">
            <h1 class="text-xl font-bold text-gray-900 mx-auto">Status Pesanan</h1>
        </div>

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-orange-500 text-white p-5 text-center">
                <p class="text-orange-100 text-sm mb-1">Nomor Pesanan</p>
                <h2 class="text-3xl font-black tracking-wider">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
            </div>
            
            <div class="p-5">
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                    <span class="text-gray-500 text-sm font-bold">Status Pesanan</span>
                </div>
                
                <!-- Tracking Timeline (GoFood/ShopeeFood Style) -->
                <div class="mb-8 px-2 relative" x-data="{ 
                    playNotification() {
                        try {
                            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                            const oscillator = audioCtx.createOscillator();
                            const gainNode = audioCtx.createGain();
                            oscillator.connect(gainNode);
                            gainNode.connect(audioCtx.destination);
                            oscillator.type = 'sine';
                            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                            oscillator.frequency.setValueAtTime(1108.73, audioCtx.currentTime + 0.1); // C#6
                            gainNode.gain.setValueAtTime(1, audioCtx.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
                            oscillator.start(audioCtx.currentTime);
                            oscillator.stop(audioCtx.currentTime + 0.5);
                            
                            // Show toast
                            let toast = document.createElement('div');
                            toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-orange-600 text-white px-6 py-3 rounded-full shadow-2xl z-50 font-bold transition-all';
                            toast.innerHTML = '🔔 Status pesanan Anda diperbarui!';
                            document.body.appendChild(toast);
                            setTimeout(() => { toast.classList.add('opacity-0'); setTimeout(() => toast.remove(), 500); }, 3000);
                        } catch(e) {}
                    }
                }"
                @order-updated.window="playNotification()">
                    @php
                        $steps = [
                            'waiting_verification' => ['label' => 'Menunggu Verifikasi', 'desc' => 'Kasir sedang memeriksa pesanan'],
                            'verified' => ['label' => 'Pesanan Diterima', 'desc' => 'Pesanan Anda sudah masuk antrean'],
                            'cooking' => ['label' => 'Sedang Dimasak', 'desc' => 'Koki sedang menyiapkan pesanan Anda'],
                            'ready' => ['label' => 'Siap Disajikan', 'desc' => 'Pesanan siap diantar ke meja Anda'],
                            'completed' => ['label' => 'Selesai', 'desc' => 'Selamat menikmati hidangan!'],
                        ];
                        $stepKeys = array_keys($steps);
                        $currentIndex = array_search($order->status, $stepKeys);
                        if ($currentIndex === false && $order->status === 'waiting_payment') $currentIndex = -1;
                    @endphp

                    <div class="relative border-l-2 border-gray-200 ml-3 md:ml-4 space-y-6">
                        @foreach($steps as $key => $step)
                            @php
                                $index = array_search($key, $stepKeys);
                                $isPast = $index < $currentIndex;
                                $isCurrent = $index === $currentIndex;
                                $isFuture = $index > $currentIndex;
                            @endphp
                            <div class="relative pl-6">
                                <!-- Bullet -->
                                <div class="absolute rounded-full border-2 bg-white flex items-center justify-center transition-all duration-500
                                    {{ $isPast ? 'border-orange-500 bg-orange-500' : '' }}
                                    {{ $isCurrent ? 'border-orange-500 bg-white' : '' }}
                                    {{ $isFuture ? 'border-gray-300' : '' }}"
                                    style="width: 16px; height: 16px; left: -9px; top: 4px; {{ $isCurrent ? 'box-shadow: 0 0 0 4px rgba(255,237,213,1);' : '' }}">
                                    @if($isCurrent)
                                        <div class="rounded-full bg-orange-500 animate-pulse" style="width: 8px; height: 8px;"></div>
                                    @elseif($isPast)
                                        <svg class="text-white" style="width: 10px; height: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                <!-- Text -->
                                <div>
                                    <h4 class="font-bold text-sm {{ $isPast || $isCurrent ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['label'] }}</h4>
                                    <p class="text-xs mt-0.5 {{ $isCurrent ? 'text-orange-600 font-medium' : 'text-gray-400' }}">{{ $step['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4 pb-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-sm">Informasi Meja</h3>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Pemesan</span>
                        <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Nomor Meja</span>
                        <span class="font-bold text-gray-900">{{ $order->table->table_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Waktu Pesan</span>
                        <span class="font-bold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Detail Pesanan</h3>
                    <div class="space-y-3">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex justify-between items-start">
                                <div class="flex items-start space-x-3">
                                    <div class="w-6 h-6 rounded bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold">{{ $detail->quantity }}x</div>
                                    <span class="text-sm text-gray-800">{{ $detail->bundle_id ? $detail->bundle->name . ' (Paket)' : $detail->menu->name }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-5 flex justify-between items-center border-t border-gray-100">
                <span class="font-bold text-gray-900">Total Tagihan</span>
                <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            
            @if($order->status === 'waiting_payment')
                <div class="p-5 border-t border-red-100 bg-red-50"
                        x-data="{ isDropping: false, isCompressing: false,
                            async handleUpload(file) {
                                if (!file) return;
                                this.isCompressing = true;
                                try {
                                    const compressedFile = await window.compressImage(file);
                                    this.isCompressing = false;
                                    $wire.upload('payment_proof', compressedFile, 
                                        () => {},
                                        () => {},
                                        (event) => {}
                                    );
                                } catch (e) {
                                    console.error(e);
                                    $wire.upload('payment_proof', file);
                                    this.isCompressing = false;
                                }
                            }
                        }">
                    <div class="text-center mb-4">
                        <h3 class="text-red-700 font-bold mb-1">Pembayaran Ditolak</h3>
                        <p class="text-red-600 text-xs">Bukti pembayaran Anda tidak valid. Silakan unggah ulang bukti yang benar.</p>
                    </div>
                    
                    <form wire:submit.prevent="reuploadPayment">
                        <label 
                            for="payment-proof-dropzone" 
                            x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="
                                isDropping = false; 
                                if ($event.dataTransfer.files.length > 0) {
                                    handleUpload($event.dataTransfer.files[0]);
                                } else {
                                    let html = $event.dataTransfer.getData('text/html');
                                    if (html) {
                                        let div = document.createElement('div');
                                        div.innerHTML = html;
                                        let img = div.querySelector('img');
                                        if (img && img.src) {
                                            fetch(img.src)
                                                .then(res => res.blob())
                                                .then(blob => {
                                                    let f = new File([blob], 'payment_proof_dropped.jpg', {type: blob.type});
                                                    handleUpload(f);
                                                }).catch(err => {
                                                    console.error(err);
                                                    alert('Gagal mengambil gambar dari browser.');
                                                });
                                        }
                                    }
                                }
                            "
                            x-bind:class="isDropping ? 'border-orange-500 bg-orange-100' : 'border-gray-300 bg-white hover:bg-gray-50'"
                            class="flex flex-col items-center justify-center w-full min-h-[10rem] p-2 border-2 border-dashed rounded-xl cursor-pointer transition overflow-hidden mb-4"
                        >
                            @if($payment_proof)
                                <img src="{{ $payment_proof->temporaryUrl() }}" class="w-full h-full object-contain rounded-lg">
                            @else
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <p class="text-sm text-gray-500 font-semibold mb-1">Unggah Bukti Baru</p>
                                    <p class="text-xs text-gray-400">Klik atau Drag & Drop (Max. 50MB)</p>
                                </div>
                            @endif
                            <input id="payment-proof-dropzone" type="file" x-on:change="handleUpload($event.target.files[0])" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only">
                        </label>
                        <div x-show="isCompressing" style="display: none;" class="text-xs text-orange-500 mb-2 text-center animate-pulse w-full">Mengompresi gambar...</div>
                        <div wire:loading wire:target="payment_proof" class="text-xs text-orange-500 mb-2 text-center animate-pulse w-full">Memuat gambar...</div>
                        @error('payment_proof') <span class="text-red-500 text-xs mb-2 block text-center">{{ $message }}</span> @enderror
                        
                        <button type="submit" wire:loading.attr="disabled" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl transition shadow-sm active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="reuploadPayment">Kirim Ulang Bukti</span>
                            <span wire:loading wire:target="reuploadPayment">Mengirim...</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
        
        @if($order->status === 'completed')
        {{-- Download Struk --}}
        <div class="rounded-2xl p-5 mb-4 text-center shadow-sm border" style="background-color: #f8fafc; border-color: #e2e8f0;">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3" style="background-color: #e2e8f0; color: #0f172a;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            <h3 class="font-bold mb-2" style="color: #0f172a;">Simpan Bukti Pesanan</h3>
            <p class="text-sm mb-4 leading-relaxed" style="color: #334155;">Penting! Unduh struk ini ke perangkat Anda sebelum menutup halaman agar riwayat pesanan Anda tidak hilang.</p>
            <a href="{{ route('order.print', $order->id) }}?download=1" target="_blank" class="inline-flex justify-center items-center w-full font-bold py-3 px-4 rounded-xl transition shadow-sm space-x-2" style="background-color: #000000; color: #ffffff;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh Gambar Struk</span>
            </a>
        </div>

        {{-- Tombol Selesai & Tinggalkan Meja --}}
        <div class="rounded-2xl p-5 mb-8 text-center shadow-sm border border-green-100 bg-green-50">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3 bg-green-100">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="font-bold mb-1 text-green-800">Pesanan Selesai!</h3>
            <p class="text-sm text-green-700 mb-4 leading-relaxed">Terima kasih sudah makan di Rumpo Cafe 🙏<br>Tekan tombol di bawah jika Anda sudah selesai dan ingin meninggalkan meja.</p>
            <button
                wire:click="leaveTable"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="w-full bg-green-600 hover:bg-green-700 active:scale-95 text-white font-bold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span wire:loading.remove wire:target="leaveTable">Saya Sudah Selesai &amp; Tinggalkan Meja</span>
                <span wire:loading wire:target="leaveTable">Memproses...</span>
            </button>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-sm text-blue-800">
                    <strong>Penting:</strong> Harap jangan tutup halaman ini sebelum pesanan selesai atau Anda menyimpan <a href="{{ route('order.print', $order->id) }}" target="_blank" class="underline font-bold text-blue-700 hover:text-blue-900">struk sementara</a> Anda.
                </div>
            </div>
        </div>
        <div class="text-center pb-8">
            <p class="text-gray-500 text-sm mb-4">Halaman ini akan otomatis diperbarui saat status pesanan berubah.</p>
            <div class="inline-flex items-center space-x-2 text-orange-600 bg-orange-50 px-4 py-2 rounded-full animate-pulse">
                <div class="w-2 h-2 bg-orange-600 rounded-full"></div>
                <span class="text-xs font-bold">Menunggu update...</span>
            </div>
        @endif
    </div>
</div>
