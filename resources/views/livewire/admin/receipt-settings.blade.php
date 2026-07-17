<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Setting Struk</h2>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
        <form wire:submit.prevent="save" class="space-y-8">
            <!-- Hardware Settings -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Pengaturan Printer & Kertas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Printer</label>
                        <select wire:model.live="receipt_printer_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="thermal">Thermal (Roll Kertas)</option>
                            <option value="standard">Standard (A4/Kertas Biasa)</option>
                        </select>
                        @error('receipt_printer_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas</label>
                        <select wire:model="receipt_paper_size" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" {{ $receipt_printer_type === 'standard' ? 'disabled' : '' }}>
                            <option value="58mm">58mm (Printer Kasir Kecil)</option>
                            <option value="80mm">80mm (Printer Kasir Besar)</option>
                            @if($receipt_printer_type === 'standard')
                                <option value="a4">Cetak Default (A4)</option>
                            @endif
                        </select>
                        @error('receipt_paper_size') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Store Info -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Informasi Toko (Tampil di Struk)</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko *</label>
                        <input type="text" wire:model="receipt_store_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        @error('receipt_store_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko *</label>
                        <textarea wire:model="receipt_address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                        @error('receipt_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" wire:model="receipt_phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('receipt_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="receipt_email" placeholder="opsional@email.com" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('receipt_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="text" wire:model="receipt_website" placeholder="www.rumpocafe.com" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('receipt_website') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Media Sosial (IG, FB, TikTok)</label>
                            <input type="text" wire:model="receipt_social_media" placeholder="@rumpocafe" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('receipt_social_media') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" wire:loading.attr="disabled" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-md transition active:scale-95 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
