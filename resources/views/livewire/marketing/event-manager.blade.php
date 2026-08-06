<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kelola Promo Event Musiman</h2>
            <p class="text-sm text-gray-500">Kelola banner, headline & tema promo spesial seperti Valentine, Kemerdekaan, Natal, dll.</p>
        </div>
        <button wire:click="openModal" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Event Baru
        </button>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Table List Event -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Event &amp; Tema</th>
                        <th class="py-4 px-6">Headline &amp; Deskripsi</th>
                        <th class="py-4 px-6">Kode Promo / Diskon</th>
                        <th class="py-4 px-6">Periode</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($event->banner_image)
                                        <img src="{{ Storage::url($event->banner_image) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                            🎉
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $event->title }}</div>
                                        @php
                                            $themeBadges = [
                                                'valentine' => 'bg-pink-100 text-pink-800 border-pink-200',
                                                'kemerdekaan' => 'bg-red-100 text-red-800 border-red-200',
                                                'natal' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'general' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            ];
                                        @endphp
                                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $themeBadges[$event->theme] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($event->theme) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-gray-800">{{ $event->headline ?: '-' }}</div>
                                <div class="text-xs text-gray-500 line-clamp-1">{{ $event->description ?: '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($event->coupon_code)
                                    <span class="font-mono bg-gray-100 text-gray-800 px-2 py-1 rounded border text-xs font-bold">{{ $event->coupon_code }}</span>
                                @endif
                                @if($event->discount_percentage > 0)
                                    <span class="text-xs font-bold text-green-600 ml-1">Diskon {{ $event->discount_percentage }}%</span>
                                @endif
                                @if(!$event->coupon_code && $event->discount_percentage <= 0)
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-600">
                                <div><span class="font-semibold">Mulai:</span> {{ $event->start_date ? $event->start_date->format('d M Y H:i') : 'Langsung' }}</div>
                                <div><span class="font-semibold">Selesai:</span> {{ $event->end_date ? $event->end_date->format('d M Y H:i') : 'Tanpa Batas' }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button wire:click="toggleActive({{ $event->id }})" class="px-3 py-1 rounded-full text-xs font-bold transition {{ $event->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $event->is_active ? 'Aktif' : 'Non-aktif' }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button wire:click="edit({{ $event->id }})" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Edit</button>
                                <button wire:click="delete({{ $event->id }})" wire:confirm="Yakin ingin menghapus promo event ini?" class="text-red-600 hover:text-red-800 font-semibold text-xs">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                Belum ada Promo Event Musiman. Klik tombol di atas untuk menambah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $events->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl transition-all">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Edit Promo Event' : 'Tambah Promo Event Baru' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Event *</label>
                        <input type="text" wire:model="title" placeholder="Contoh: Promo Spesial Valentine" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tema Visual *</label>
                            <select wire:model="theme" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="valentine">💖 Valentine (Pink / Merah)</option>
                                <option value="kemerdekaan">🇮🇩 Kemerdekaan 17 Agustus (Merah Putih)</option>
                                <option value="natal">🎄 Natal &amp; Tahun Baru (Hijau / Merah)</option>
                                <option value="general">✨ Umum / General Promo</option>
                            </select>
                            @error('theme') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Voucher (Opsional)</label>
                            <input type="text" wire:model="coupon_code" placeholder="Misal: LOVE2026" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm uppercase">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Headline Menarik (Pop-up Hook)</label>
                        <input type="text" wire:model="headline" placeholder="Contoh: Manisnya Kasih Sayang, Diskon 20% Pasangan!" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Promo</label>
                        <textarea wire:model="description" rows="2" placeholder="Jelaskan detail event promo..." class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Diskon (%)</label>
                            <input type="number" step="0.01" wire:model="discount_percentage" placeholder="0" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Batas Pengguna</label>
                            <input type="number" wire:model="usage_limit" placeholder="Batas Promo" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                        </div>
                        <div x-data="{ isDropping: false }"
                             @dragover.prevent="isDropping = true"
                             @dragleave.prevent="isDropping = false"
                             @drop.prevent="isDropping = false; if($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true })); }">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Gambar Banner / Poster</label>
                            <div :class="{ 'border-orange-500 bg-orange-50': isDropping, 'border-gray-300 bg-white': !isDropping }"
                                 class="relative border-2 border-dashed rounded-xl p-2 transition-colors duration-200">
                                <input x-ref="fileInput" type="file" wire:model="banner_image" accept="image/*" class="relative z-10 w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200 cursor-pointer">
                                <div class="absolute inset-0 pointer-events-none flex items-center justify-end pr-4" x-show="!isDropping">
                                    <span class="text-xs text-gray-400 font-medium">Atau Drag & Drop file ke sini</span>
                                </div>
                                <div class="absolute inset-0 z-20 pointer-events-none flex items-center justify-center bg-orange-50/90 rounded-xl" x-show="isDropping" style="display: none;">
                                    <span class="text-orange-600 font-bold text-sm">Lepaskan Gambar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Waktu Mulai</label>
                            <input type="datetime-local" wire:model="start_date" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Waktu Selesai</label>
                            <input type="datetime-local" wire:model="end_date" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-700 font-bold">Aktifkan Event Ini Sekarang</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-sm hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md transition">Simpan Event</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
