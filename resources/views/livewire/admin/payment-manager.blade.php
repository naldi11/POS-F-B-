<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Pembayaran</h2>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center max-w-2xl mx-auto w-full">
        <div class="text-center mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Upload QRIS Pembayaran</h3>
            <p class="text-gray-500 text-sm">Unggah gambar QRIS asli dari bank atau e-wallet (OVO, GoPay, Dana) toko Anda.</p>
        </div>

        <div class="w-full max-w-sm mb-6 flex-grow">
            <form wire:submit.prevent="saveQrisImage" class="flex flex-col h-full">
                
                <label 
                    for="qris-dropzone" 
                    x-data="{ isDropping: false }"
                    x-on:dragover.prevent="isDropping = true"
                    x-on:dragleave.prevent="isDropping = false"
                    x-on:drop.prevent="
                        isDropping = false; 
                        if ($event.dataTransfer.files.length > 0) {
                            $wire.upload('qris_image', $event.dataTransfer.files[0]);
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
                                            let f = new File([blob], 'qris_dropped.jpg', {type: blob.type});
                                            $wire.upload('qris_image', f);
                                        }).catch(err => {
                                            console.error(err);
                                            alert('Gagal mengambil gambar dari browser. Coba simpan (Save as) gambar terlebih dahulu.');
                                        });
                                }
                            }
                        }
                    "
                    x-bind:class="isDropping ? 'border-orange-500 bg-orange-100' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                    class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer transition relative overflow-hidden mb-6"
                >
                    @if($qris_image)
                        <img src="{{ $qris_image->temporaryUrl() }}" class="w-full h-full object-contain rounded-lg p-2 bg-white">
                    @elseif($saved_qris_url)
                        <img src="{{ $saved_qris_url }}" class="w-full h-full object-contain rounded-lg p-2 bg-white">
                    @else
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik atau Drag & Drop</span> untuk upload</p>
                            <p class="text-xs text-gray-500">PNG, JPG or JPEG (Max. 2MB)</p>
                        </div>
                    @endif
                    <input id="qris-dropzone" type="file" wire:model="qris_image" accept="image/*" class="hidden">
                </label>
                <div wire:loading wire:target="qris_image" class="text-sm text-orange-500 mb-4 text-center animate-pulse w-full">Memuat pratinjau gambar...</div>
                @error('qris_image') <span class="text-red-500 text-xs mb-4 block text-center">{{ $message }}</span> @enderror

                <button type="submit" wire:loading.attr="disabled" wire:target="saveQrisImage" class="w-full mt-auto flex justify-center items-center space-x-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-md active:scale-95 disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span wire:loading.remove wire:target="saveQrisImage">Simpan QRIS</span>
                    <span wire:loading wire:target="saveQrisImage">Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
</div>
