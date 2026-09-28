<div class="min-h-screen bg-gray-50 pt-6 pb-24 relative"
     wire:poll.2s="refreshOrder"
     x-data="{
         currentStatus: @js($order->status),
         showLeaveModal: false,
         playNotification() {
             try {
                 const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                 const oscillator = audioCtx.createOscillator();
                 const gainNode = audioCtx.createGain();
                 oscillator.connect(gainNode);
                 gainNode.connect(audioCtx.destination);
                 oscillator.type = 'sine';
                 oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
                 oscillator.frequency.setValueAtTime(1108.73, audioCtx.currentTime + 0.1);
                 gainNode.gain.setValueAtTime(0.4, audioCtx.currentTime);
                 gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
                 oscillator.start(audioCtx.currentTime);
                 oscillator.stop(audioCtx.currentTime + 0.5);
             } catch(e) {}
         },
         playSuccessChime() {
             try {
                 const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                 const notes = [523.25, 659.25, 783.99, 1046.50];
                 notes.forEach((freq, idx) => {
                     const osc = audioCtx.createOscillator();
                     const gain = audioCtx.createGain();
                     osc.connect(gain);
                     gain.connect(audioCtx.destination);
                     osc.type = 'sine';
                     osc.frequency.setValueAtTime(freq, audioCtx.currentTime + idx * 0.08);
                     gain.gain.setValueAtTime(0.25, audioCtx.currentTime + idx * 0.08);
                     gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + idx * 0.08 + 0.35);
                     osc.start(audioCtx.currentTime + idx * 0.08);
                     osc.stop(audioCtx.currentTime + idx * 0.08 + 0.35);
                 });
             } catch(e) {}
         },
         showToast(msg, bg = '#ef4444') {
             let toast = document.createElement('div');
             toast.style.cssText = `position: fixed; top: 16px; left: 50%; transform: translateX(-50%); background-color: ${bg}; color: white; padding: 12px 24px; border-radius: 9999px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25); z-index: 99999; font-weight: bold; transition: opacity 0.5s; width: max-content; font-family: sans-serif; font-size: 14px; text-align: center;`;
             toast.innerHTML = msg;
             document.body.appendChild(toast);
             setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 3500);
         }
     }"
     @order-confirmed.window="playSuccessChime()"
>
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
                <div class="mb-8 px-2 relative">
                    @php
                        $steps = [
                            'waiting_verification' => ['label' => 'Menunggu Verifikasi', 'desc' => 'Kasir sedang memeriksa pesanan'],
                            'verified' => ['label' => 'Pesanan Diterima', 'desc' => 'Pesanan Anda sudah masuk antrean'],
                            'cooking' => ['label' => 'Sedang Dimasak', 'desc' => 'Koki sedang menyiapkan pesanan Anda'],
                            'ready' => ['label' => 'Siap Disajikan', 'desc' => 'Pesanan siap / sedang diantar ke meja Anda'],
                            'waiting_confirmation' => ['label' => 'Menunggu Konfirmasi', 'desc' => 'Pesanan tiba di meja, mohon konfirmasi'],
                            'completed' => ['label' => 'Selesai', 'desc' => 'Selamat menikmati hidangan!'],
                        ];
                        $stepKeys = array_keys($steps);
                        $currentIndex = array_search($order->status, $stepKeys);
                        if ($order->status === 'ready') {
                            $currentIndex = 4; // Aktif di tahap Menunggu Konfirmasi
                        }
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
                            <div class="relative" style="padding-left: 32px; min-height: 24px;">
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
        
        @if(in_array($order->status, ['waiting_confirmation', 'ready']))
        {{-- Card Banner Konfirmasi Pelanggan --}}
        <div class="rounded-3xl p-6 mb-6 text-center shadow-xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 relative overflow-hidden">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-3 bg-gradient-to-tr from-amber-500 to-orange-500 text-white shadow-lg shadow-orange-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <div class="inline-block px-3.5 py-1 bg-amber-500/15 border border-amber-500/30 rounded-full text-xs font-black text-amber-800 uppercase tracking-wider mb-2">
                Menunggu Konfirmasi Anda
            </div>

            <h3 class="text-xl font-black text-gray-900 mb-1">Pesanan Telah Tiba di Meja?</h3>
            <p class="text-xs text-gray-600 mb-5 leading-relaxed font-medium">
                Hidangan Anda telah disajikan oleh staf kami ke <span class="font-bold text-gray-900">Meja {{ $order->table->table_number }}</span>.<br>
                Silakan periksa kelengkapan hidangan di meja Anda.
            </p>

            <button
                type="button"
                wire:click="confirmOrderReceived"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="w-full bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 hover:from-orange-600 hover:to-amber-600 active:scale-95 text-white font-extrabold py-4 px-6 rounded-2xl transition shadow-lg shadow-orange-500/30 flex items-center justify-center space-x-2 text-base"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span wire:loading.remove wire:target="confirmOrderReceived">✓ Konfirmasi Pesanan Sudah Diterima</span>
                <span wire:loading wire:target="confirmOrderReceived">Memproses Konfirmasi...</span>
            </button>
            <p class="text-[11px] text-gray-400 mt-3">
                Tekan tombol di atas untuk menyelesaikan pesanan &amp; melihat struk resmi Anda.
            </p>
        </div>
        @endif

        @if($order->status === 'completed')
        {{-- Banner Selesai Dikonfirmasi --}}
        <div class="rounded-3xl p-5 mb-4 text-center bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/20 mb-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-lg font-black mb-0.5">Pesanan Selesai Dikonfirmasi!</h3>
            <p class="text-xs text-emerald-100 font-medium">Selamat menikmati hidangan Anda di Rumpo Cafe 🎉</p>
        </div>

        {{-- Download Struk --}}
        <div class="rounded-2xl p-5 mb-4 text-center shadow-sm border bg-white border-gray-200">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3 bg-gray-100 text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            <h3 class="font-bold mb-1 text-gray-900">Struk Resmi Pembayaran</h3>
            <p class="text-xs mb-4 leading-relaxed text-gray-600">Simpan atau unduh gambar struk ini ke perangkat Anda sebagai bukti transaksi yang sah.</p>
            <div class="space-y-2">
                <a href="{{ route('order.print', $order->id) }}?download=1" target="_blank" class="inline-flex justify-center items-center w-full font-bold py-3.5 px-4 rounded-xl transition shadow-sm space-x-2 bg-gray-900 hover:bg-black text-white active:scale-95 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh Gambar Struk</span>
                </a>
                <a href="{{ route('order.print', $order->id) }}" target="_blank" class="inline-flex justify-center items-center w-full font-semibold py-2.5 px-4 rounded-xl transition space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Buka / Cetak Struk</span>
                </a>
            </div>
        </div>

        {{-- Kartu Status Meja & Tombol Tinggalkan Meja (Wajib Saat Selesai Makan) --}}
        <div class="rounded-3xl p-6 mb-6 text-center shadow-lg border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 via-teal-50 to-green-100 relative overflow-hidden">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-3 bg-emerald-500 text-white shadow-md">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-emerald-100 border border-emerald-300 rounded-full text-xs font-bold text-emerald-800 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Meja {{ $order->table->table_number }} Sedang Anda Gunakan</span>
            </div>

            <h3 class="text-xl font-black text-emerald-900 mb-1">Sudah Selesai Makan?</h3>
            <p class="text-xs text-emerald-700 mb-5 leading-relaxed font-medium">
                Terima kasih sudah menikmati hidangan kami! 🙏<br>
                Saat Anda siap meninggalkan meja, <strong>silakan tekan tombol di bawah</strong> agar status meja Anda kembali kosong dan siap untuk pelanggan baru.
            </p>

            <button
                type="button"
                @click="showLeaveModal = true"
                class="w-full bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 active:scale-95 text-white font-extrabold py-4 px-4 rounded-2xl transition shadow-xl flex items-center justify-center space-x-2 text-sm"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Saya Sudah Selesai Makan &amp; Tinggalkan Meja</span>
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
        </div>
        @endif
    </div>

    <!-- MODAL POPUP OTOMATIS KONFIRMASI PENERIMAAN PESANAN -->
    @if(in_array($order->status, ['waiting_confirmation', 'ready']))
    <div 
        x-data="{ 
            modalOpen: true,
            init() {
                this.$nextTick(() => {
                    playNotification();
                });
            }
        }"
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 99999;"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

        <div class="min-h-full flex items-center justify-center p-4 text-center">
            <div 
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all max-w-sm w-full border border-amber-200"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            >
                <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 p-6 text-center text-white relative">
                    <button 
                        @click="modalOpen = false" 
                        type="button" 
                        class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 rounded-full p-1.5 transition"
                        title="Tutup dialog"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md mb-3 shadow-inner">
                        <svg class="w-10 h-10 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    
                    <span class="inline-block px-3 py-0.5 bg-black/20 rounded-full text-[11px] font-black uppercase tracking-wider text-amber-100 mb-1">
                        Pesanan Telah Tiba
                    </span>
                    <h3 class="text-2xl font-black tracking-tight" id="modal-title">Pesanan Sudah Diantar!</h3>
                    <p class="text-xs text-amber-100 mt-1 font-medium">Staf kami telah menyajikan hidangan ke <span class="font-extrabold text-white underline">Meja {{ $order->table->table_number }}</span></p>
                </div>

                <div class="p-6">
                    <p class="text-xs text-gray-600 mb-4 text-center leading-relaxed">
                        Silakan periksa hidangan di meja Anda. Jika pesanan sudah lengkap dan sesuai, klik tombol konfirmasi di bawah:
                    </p>

                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-3.5 mb-5 max-h-48 overflow-y-auto">
                        <div class="text-[11px] font-bold text-amber-900 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Daftar Hidangan Anda:</span>
                            <span class="text-amber-700 font-semibold">{{ $order->orderDetails->sum('quantity') }} Item</span>
                        </div>
                        <ul class="space-y-1.5 text-xs text-gray-800">
                            @foreach($order->orderDetails as $detail)
                                <li class="flex items-center justify-between py-1 border-b border-amber-100/60 last:border-b-0">
                                    <span class="flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="font-semibold">{{ $detail->quantity }}x</span>
                                        <span>{{ $detail->bundle_id ? $detail->bundle->name . ' (Paket)' : $detail->menu->name }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <button
                        type="button"
                        wire:click="confirmOrderReceived"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 hover:from-orange-600 hover:to-amber-600 active:scale-95 text-white font-extrabold py-4 px-4 rounded-2xl shadow-lg shadow-orange-500/30 transition flex items-center justify-center space-x-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span wire:loading.remove wire:target="confirmOrderReceived">✓ Konfirmasi Pesanan Diterima</span>
                        <span wire:loading wire:target="confirmOrderReceived">Menyelesaikan Pesanan...</span>
                    </button>

                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="w-full mt-2 py-2.5 text-xs text-gray-500 hover:text-gray-700 font-semibold text-center transition"
                    >
                        Periksa Nanti (Tutup Dialog)
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL POPUP KONFIRMASI TINGGALKAN MEJA -->
    <div 
        x-show="showLeaveModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="leave-modal-title" 
        role="dialog" 
        aria-modal="true"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="showLeaveModal = false"></div>

        <div class="min-h-full flex items-center justify-center p-4 text-center">
            <div 
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all max-w-sm w-full border border-emerald-200"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            >
                <div class="bg-gradient-to-br from-emerald-600 to-teal-600 p-6 text-center text-white relative">
                    <button 
                        @click="showLeaveModal = false" 
                        type="button" 
                        class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 rounded-full p-1.5 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md mb-3 shadow-inner">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    
                    <h3 class="text-2xl font-black tracking-tight" id="leave-modal-title">Tinggalkan Meja?</h3>
                    <p class="text-xs text-emerald-100 mt-1 font-medium">Meja {{ $order->table->table_number }} akan dikosongkan</p>
                </div>

                <div class="p-6">
                    <p class="text-xs text-gray-600 mb-5 leading-relaxed text-center">
                        Apakah Anda sudah selesai menikmati hidangan dan siap meninggalkan kafe? Status <span class="font-bold text-gray-900">Meja {{ $order->table->table_number }}</span> akan otomatis diubah menjadi <strong>Tersedia (Kosong)</strong> untuk pelanggan lain.
                    </p>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl mb-5 text-[11px] text-amber-800 flex items-start space-x-2">
                        <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Pastikan Anda telah mengunduh atau menyimpan struk pesanan Anda sebelum meninggalkan halaman ini.</span>
                    </div>

                    <div class="space-y-2">
                        <button
                            type="button"
                            wire:click="leaveTable"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-95 text-white font-extrabold py-3.5 px-4 rounded-xl shadow-lg transition flex items-center justify-center space-x-2 text-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span wire:loading.remove wire:target="leaveTable">Ya, Saya Sudah Selesai &amp; Kosongkan Meja</span>
                            <span wire:loading wire:target="leaveTable">Mengosongkan Meja...</span>
                        </button>

                        <button
                            type="button"
                            @click="showLeaveModal = false"
                            class="w-full py-2.5 text-xs text-gray-500 hover:text-gray-700 font-semibold text-center transition"
                        >
                            Batal (Masih di Meja)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
