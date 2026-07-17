<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Menu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg backdrop-blur-lg bg-opacity-80 p-6">
                @if (session()->has('message'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium">{{ session('message') }}</p>
                    </div>
                @endif
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Daftar Menu</h3>
                    <button wire:click="create" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-5 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Menu
                    </button>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Gambar</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Kategori</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Nama Menu</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Harga</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900 text-center">Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            @foreach ($menus as $menu)
                                <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition duration-150">
                                    <td class="px-6 py-4">
                                        @if($menu->image)
                                            <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}" class="w-14 h-14 object-cover rounded-lg shadow-sm border border-gray-100">
                                        @else
                                            <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">No Image</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $menu->category->name }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $menu->name }}</td>
                                    <td class="px-6 py-4 text-orange-600 font-medium">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-full {{ $menu->is_available ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="edit({{ $menu->id }})" class="font-medium text-orange-500 hover:text-orange-700 transition-colors">Edit</button>
                                            <button wire:click="delete({{ $menu->id }})" class="font-medium text-gray-400 hover:text-red-500 transition-colors">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $menus->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[70vh] overflow-y-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Data Menu</h3>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                            <select wire:model="category_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Menu</label>
                            <input type="text" wire:model="name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('name') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea wire:model="description" rows="3" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition"></textarea>
                            @error('description') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                            <input type="number" wire:model="price" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('price') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Menu</label>
                            
                            <!-- Dropzone -->
                            <div x-data="{
                                    isDropping: false,
                                    handleDrop(e) {
                                        this.isDropping = false;
                                        if (e.dataTransfer.files.length > 0) {
                                            @this.upload('image', e.dataTransfer.files[0]);
                                            return;
                                        }
                                        
                                        // Prioritize extracting image from HTML (useful for Google Images)
                                        let html = e.dataTransfer.getData('text/html');
                                        if (html) {
                                            let div = document.createElement('div');
                                            div.innerHTML = html;
                                            let img = div.querySelector('img');
                                            if (img && img.src) {
                                                @this.uploadFromUrl(img.src);
                                                return;
                                            }
                                        }
                                        
                                        // Fallback to plain URL
                                        let url = e.dataTransfer.getData('URL') || e.dataTransfer.getData('text/uri-list');
                                        if (url) {
                                            @this.uploadFromUrl(url);
                                        }
                                    }
                                }"
                                @dragover.prevent="isDropping = true"
                                @dragleave.prevent="isDropping = false"
                                @drop.prevent="handleDrop($event)"
                                :class="{'border-orange-500 bg-orange-50': isDropping, 'border-gray-300 bg-gray-50': !isDropping}"
                                class="relative border-2 border-dashed rounded-xl p-6 text-center transition-colors flex flex-col items-center justify-center min-h-[120px]">
                                
                                <input type="file" wire:model="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" title="Klik atau Drop gambar di sini">
                                
                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <p class="text-sm text-gray-500 font-medium">Klik untuk memilih file, atau <span class="text-orange-500 font-bold">Drag & Drop</span> gambar ke sini</p>
                                <p class="text-xs text-gray-400 mt-1">Bisa drop gambar dari komputer atau dari browser internet</p>

                                <div wire:loading wire:target="image" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center rounded-xl z-20">
                                    <div class="text-sm text-orange-500 font-bold animate-pulse flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengunggah...
                                    </div>
                                </div>
                                <div wire:loading wire:target="uploadFromUrl" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center rounded-xl z-20">
                                    <div class="text-sm text-orange-500 font-bold animate-pulse flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengunduh dari URL...
                                    </div>
                                </div>
                            </div>
                            @error('image') <span class="text-red-500 text-xs italic block mt-1">{{ $message }}</span> @enderror
                            
                            @if ($image)
                                <div class="mt-3 flex items-center bg-green-50 p-3 rounded-xl border border-green-200 shadow-sm relative">
                                    <img src="{{ $image->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-100 mr-4">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-green-700">Gambar baru siap diunggah</p>
                                    </div>
                                    <button type="button" wire:click="removeImage" class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full p-1 shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @elseif ($downloadedImage)
                                <div class="mt-3 flex items-center bg-blue-50 p-3 rounded-xl border border-blue-200 shadow-sm relative">
                                    <img src="{{ Storage::url($downloadedImage) }}" class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-100 mr-4">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-blue-700">Gambar berhasil diunduh</p>
                                    </div>
                                    <button type="button" wire:click="removeImage" class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full p-1 shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @elseif ($existingImage)
                                <div class="mt-3 flex items-center bg-gray-50 p-3 rounded-xl border border-gray-200 shadow-sm relative">
                                    <img src="{{ Storage::url($existingImage) }}" class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-100 mr-4">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-700">Gambar saat ini</p>
                                    </div>
                                    <button type="button" wire:click="$set('existingImage', null)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full p-1 shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" wire:model="is_available" class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"><span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Tersedia</span>
                            </label>
                            @error('is_available') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-orange-500 text-sm font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto transition disabled:opacity-50">
                            Simpan Data
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-200 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
