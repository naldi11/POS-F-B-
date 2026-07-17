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

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-500 text-sm">Total Tagihan</span>
                <span class="font-bold text-gray-900 text-xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <form wire:submit.prevent="processCheckout" class="space-y-6">
            <!-- Informasi Pemesan -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div>
                    <label for="table_number" class="block text-gray-900 text-sm font-bold mb-2">Nomor Meja <span class="text-red-500">*</span></label>
                    <input type="text" id="table_number" wire:model="table_number" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition" placeholder="Contoh: 1, 2, atau A1">
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
                        <img src="{{ Storage::url($qris_image) }}" alt="QRIS" class="w-full h-auto object-contain max-h-64 rounded-lg">
                    @else
                        <svg class="w-32 h-32 text-orange-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <p class="text-sm font-medium text-orange-800 text-center">QRIS Belum Diatur</p>
                    @endif
                </div>

                <label class="block text-gray-900 text-sm font-bold mb-2">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
                <div class="flex items-center justify-center w-full">
                    <label 
                        for="dropzone-file" 
                        x-data="{ isDropping: false }"
                        x-on:dragover.prevent="isDropping = true"
                        x-on:dragleave.prevent="isDropping = false"
                        x-on:drop.prevent="
                            isDropping = false; 
                            if ($event.dataTransfer.files.length > 0) {
                                $wire.upload('payment_proof', $event.dataTransfer.files[0]);
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
                                                $wire.upload('payment_proof', f);
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
                                <p class="text-xs text-gray-500">PNG, JPG or JPEG (Max. 2MB)</p>
                            </div>
                        @endif
                        <input id="dropzone-file" type="file" wire:model="payment_proof" class="hidden" accept="image/*" />
                    </label>
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
