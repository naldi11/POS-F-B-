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
                    <span class="text-gray-500 text-sm">Status Pesanan</span>
                    @php
                        $statusColors = [
                            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
                            'waiting_verification' => 'bg-orange-100 text-orange-800',
                            'verified' => 'bg-blue-100 text-blue-800',
                            'cooking' => 'bg-purple-100 text-purple-800',
                            'ready' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-gray-100 text-gray-800',
                        ];
                        $statusLabels = [
                            'waiting_payment' => 'Menunggu Pembayaran',
                            'waiting_verification' => 'Pending (Menunggu Verifikasi)',
                            'verified' => 'Pesanan Diterima',
                            'cooking' => 'Sedang Dimasak',
                            'ready' => 'Siap Disajikan',
                            'completed' => 'Selesai',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColors[$order->status] }}">
                        {{ $statusLabels[$order->status] }}
                    </span>
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
        <div class="rounded-2xl p-5 mb-8 text-center shadow-sm border" style="background-color: #f8fafc; border-color: #e2e8f0;">
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
</div>
