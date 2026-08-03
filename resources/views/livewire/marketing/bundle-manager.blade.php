<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Paket Hemat</h2>
            <p class="text-gray-500 text-sm mt-1">Buat paket gabungan (bundle) untuk promosi menarik.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Paket
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 font-semibold text-gray-600 text-sm">Paket</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 text-sm">Isi Menu</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 text-sm">Harga Paket</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bundles as $bundle)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4">
                                    @if ($bundle->image)
                                        <img src="{{ Storage::url($bundle->image) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center border border-orange-200">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $bundle->name }}</p>
                                        <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ $bundle->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <ul class="list-disc list-inside text-sm text-gray-600">
                                    @foreach($bundle->items as $item)
                                        <li>{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu Dihapus' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-orange-600">Rp {{ number_format($bundle->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $bundle->is_active ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                    {{ $bundle->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $bundle->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete({{ $bundle->id }})" wire:confirm="Hapus paket hemat ini?" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <p>Belum ada paket hemat</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bundles->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $bundles->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if ($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 backdrop-blur-sm">
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">
                    {{ $bundleId ? 'Edit Paket Hemat' : 'Tambah Paket Hemat' }}
                </h3>
                <button wire:click="closeModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="p-6">
                <form wire:submit.prevent="store" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Paket</label>
                            <input type="text" wire:model="name" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition" placeholder="Contoh: Paket Keluarga Bahagia">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp)</label>
                            <input type="number" wire:model="price" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition" placeholder="Contoh: 50000">
                            @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select wire:model="is_active" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition" placeholder="Penjelasan paket..."></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Image -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Paket</label>
                            
                            <label for="bundle-image"
                                x-data="{ isDropping: false }"
                                x-on:dragover.prevent="isDropping = true"
                                x-on:dragleave.prevent="isDropping = false"
                                x-on:drop.prevent="
                                    isDropping = false; 
                                    if ($event.dataTransfer.files.length > 0) {
                                        $wire.upload('image', $event.dataTransfer.files[0]);
                                    }
                                "
                                @paste.window="
                                    if ($event.clipboardData.files.length > 0) {
                                        $wire.upload('image', $event.clipboardData.files[0]);
                                    }
                                "
                                x-bind:class="isDropping ? 'border-orange-500 bg-orange-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                                class="relative flex flex-col items-center justify-center w-full min-h-[12rem] p-4 border-2 border-dashed rounded-xl cursor-pointer transition overflow-hidden block">
                                
                                @if ($image)
                                    <div class="relative w-full h-40">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-contain rounded-lg">
                                        <button type="button" @click.stop.prevent="$wire.removeImage()" class="absolute top-2 right-2 z-20 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition shadow-lg" title="Hapus Gambar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @elseif ($bundleId && !$remove_existing_image && \App\Models\Bundle::find($bundleId)->image)
                                    <div class="relative w-full h-40">
                                        <img src="{{ Storage::url(\App\Models\Bundle::find($bundleId)->image) }}" class="w-full h-full object-contain rounded-lg">
                                        <button type="button" @click.stop.prevent="$wire.removeImage()" class="absolute top-2 right-2 z-20 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition shadow-lg" title="Hapus Gambar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-sm text-gray-600 font-semibold mb-1">Unggah Gambar Paket</p>
                                        <p class="text-xs text-gray-500">Klik atau Drag & Drop (Max. 2MB)</p>
                                    </div>
                                @endif
                                <input id="bundle-image" x-ref="fileInput" type="file" wire:model="image" class="sr-only" accept="image/*">
                            </label>
                            
                            <div wire:loading wire:target="image" class="text-xs text-orange-500 mt-2 flex items-center gap-1 animate-pulse">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mengunggah gambar...
                            </div>
                            @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bundle Items -->
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between mb-3 border-t border-gray-100 pt-4">
                                <label class="block text-sm font-medium text-gray-800">Isi Paket (Menu)</label>
                                <button type="button" wire:click="addBundleItem" class="text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Menu
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($bundleItems as $index => $item)
                                    <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="flex-grow">
                                            <select wire:model="bundleItems.{{ $index }}.menu_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                                <option value="">-- Pilih Menu --</option>
                                                @foreach($availableMenus as $menu)
                                                    <option value="{{ $menu->id }}">{{ $menu->name }} (Rp {{ number_format($menu->price, 0, ',', '.') }})</option>
                                                @endforeach
                                            </select>
                                            @error('bundleItems.'.$index.'.menu_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="w-24 shrink-0">
                                            <input type="number" wire:model="bundleItems.{{ $index }}.quantity" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm text-center" min="1" placeholder="Qty">
                                            @error('bundleItems.'.$index.'.quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <button type="button" wire:click="removeBundleItem({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors shrink-0 mt-0.5">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            @error('bundleItems') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal footer -->
            <div class="flex items-center justify-end p-6 border-t border-gray-100 gap-3">
                <button wire:click="closeModal" type="button" class="text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-xl text-sm px-5 py-2.5 transition-colors">
                    Batal
                </button>
                <button wire:click="store" type="button" class="text-white bg-orange-600 hover:bg-orange-700 font-medium rounded-xl text-sm px-5 py-2.5 transition-colors shadow-sm">
                    Simpan Paket
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
