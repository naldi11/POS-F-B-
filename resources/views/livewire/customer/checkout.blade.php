<div class="min-h-screen bg-gray-50 pt-6 pb-24 relative">
    <div class="max-w-md mx-auto px-4">
        
        <!-- Header -->
        <div class="flex items-center mb-6 sticky top-0 bg-gray-50 bg-opacity-90 backdrop-blur-sm pt-4 pb-2 z-10">
            <a href="{{ route('customer.cart') }}" class="p-2 mr-3 bg-white rounded-full shadow-sm hover:shadow-md transition">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Pembayaran</h1>
        </div>

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow-sm">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (session()->has('promo_message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
                <p>{{ session('promo_message') }}</p>
            </div>
        @endif

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Subtotal</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if($appliedPromo)
                    <div class="flex justify-between items-center text-green-600">
                        <span class="text-sm">Diskon ({{ $appliedPromo->code }})</span>
                        <span class="font-medium">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
            <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                <span class="text-gray-900 font-bold">Total Tagihan</span>
                <span class="font-bold text-orange-600 text-xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                @if(!$appliedPromo)
                    <div class="flex gap-2">
                        <input type="text" wire:model="promoCodeInput" placeholder="Punya kode promo?" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2 transition uppercase">
                        <button type="button" wire:click="applyPromo" class="whitespace-nowrap px-4 py-2 bg-gray-800 text-white font-medium rounded-xl hover:bg-gray-900 transition text-sm">Gunakan</button>
                    </div>
                    @error('promoCodeInput') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                @else
                    <div class="flex justify-between items-center bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                        <div class="flex items-center text-green-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Promo {{ $appliedPromo->code }} aktif
                        </div>
                        <button type="button" wire:click="removePromo" class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1">Hapus</button>
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="processCheckout" class="space-y-6">
            <!-- Informasi Pemesan -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div>
                    <label for="table_number" class="block text-gray-900 text-sm font-bold mb-2">Nomor Meja</label>
                    <input type="text" id="table_number" wire:model="table_number" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-600 font-bold text-sm rounded-xl block p-3 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Nomor meja otomatis terisi dari hasil scan QR Code.</p>
                    @if($is_occupied)
                        <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start space-x-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-amber-800 font-medium">Meja ini saat ini berstatus <strong>Terisi</strong>. Pesanan Anda tetap akan dicatat sebagai pesanan terpisah untuk meja ini.</p>
                        </div>
                    @endif
                    @error('table_number') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="customer_name" class="block text-gray-900 text-sm font-bold mb-2">Nama Pemesan <span class="text-red-500">*</span></label>
                    <input type="text" id="customer_name" wire:model="customer_name" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition" placeholder="Masukkan nama Anda">
                    @error('customer_name') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="customer_phone" class="block text-gray-900 text-sm font-bold mb-2">No. WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" id="customer_phone" wire:model="customer_phone" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition" placeholder="Contoh: 08123456789">
                    @error('customer_phone') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Metode Pembayaran & QRIS -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4 text-center">Scan QRIS untuk Membayar</h3>
                
                <div class="bg-orange-50 rounded-xl p-4 flex flex-col items-center justify-center mb-6 border-2 border-dashed border-orange-200 overflow-hidden relative">
                    @if($qris_image)
                        <img src="{{ Storage::url($qris_image) }}" alt="QRIS" class="w-full h-auto object-contain max-h-64 rounded-lg mb-4">
                        <a href="{{ Storage::url($qris_image) }}" download="QRIS_Rumpo_Cafe" class="flex items-center space-x-2 bg-white border border-gray-200 text-gray-800 text-sm font-semibold px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition active:scale-95">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Simpan QRIS ke Galeri</span>
                        </a>
                    @else
                        <svg class="w-32 h-32 text-orange-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <p class="text-sm font-medium text-orange-800 text-center">QRIS Belum Diatur</p>
                    @endif
                </div>

                <label class="block text-gray-900 text-sm font-bold mb-2">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
                <div class="flex flex-col items-center justify-center w-full"
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
                    <label 
                        for="dropzone-file" 
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
                                                alert('Gagal mengambil gambar dari browser. Coba simpan (Save as) gambar terlebih dahulu.');
                                            });
                                    }
                                }
                            }
                        "
                        x-bind:class="isDropping ? 'border-orange-500 bg-orange-100' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                        class="flex flex-col items-center justify-center w-full min-h-[10rem] p-2 border-2 border-dashed rounded-xl cursor-pointer transition overflow-hidden"
                    >
                        @if ($payment_proof)
                            <img src="{{ $payment_proof->temporaryUrl() }}" class="w-full h-auto max-h-[70vh] object-contain rounded-lg">
                        @else
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                                <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik atau Drag & Drop</span> untuk upload</p>
                                <p class="text-xs text-gray-500">PNG, JPG or JPEG (Max. 50MB)</p>
                            </div>
                        @endif
                        <input id="dropzone-file" type="file" x-on:change="handleUpload($event.target.files[0])" class="sr-only" accept="image/jpeg,image/png,image/jpg,image/webp" />
                    </label>
                    <div x-show="isCompressing" style="display: none;" class="text-sm text-orange-500 mt-2 text-center animate-pulse w-full">Mengompresi gambar...</div>
                </div>
                <div wire:loading wire:target="payment_proof" class="text-sm text-orange-500 mt-2 text-center animate-pulse w-full">Mengunggah gambar...</div>
                @error('payment_proof') <span class="text-red-500 text-xs italic mt-2 block text-center">{{ $message }}</span> @enderror
            </div>

            <!-- Checkout Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                <div class="max-w-md mx-auto">
                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center space-x-2 bg-orange-500 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:bg-orange-600 transition transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:scale-100">
                        <span wire:loading.remove wire:target="processCheckout">Selesaikan Pesanan</span>
                        <span wire:loading wire:target="processCheckout">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
