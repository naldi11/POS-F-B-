<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Promo</h2>
            <a href="{{ route('marketing.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Kembali ke Dashboard
            </a>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Card -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $editingId ? 'Edit Promo' : 'Buat Promo Baru' }}</h3>
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo</label>
                            <input type="text" wire:model="code" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 uppercase" placeholder="MISAL: DISKONMERDEKA">
                            @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon</label>
                            <select wire:model="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Potongan Tetap (Rp)</option>
                            </select>
                            @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Diskon</label>
                            <input type="number" step="0.01" wire:model="value" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="10 (jika %), 10000 (jika Rp)">
                            @error('value') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Pembelian (Opsional)</label>
                            <input type="number" step="0.01" wire:model="min_purchase" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="0">
                            @error('min_purchase') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Berlaku</label>
                                <input type="datetime-local" wire:model="valid_from" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                @error('valid_from') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berakhir Pada</label>
                                <input type="datetime-local" wire:model="valid_until" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                @error('valid_until') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-6 flex items-center">
                            <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Promo Aktif</label>
                            @error('is_active') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                {{ $editingId ? 'Update Promo' : 'Simpan Promo' }}
                            </button>
                            @if($editingId)
                                <button type="button" wire:click="resetInputFields" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition">
                                    Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Aktif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($promotions as $promo)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-800">{{ $promo->code }}</div>
                                        <div class="text-xs text-gray-500">Min. Beli: Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($promo->type === 'percentage')
                                            {{ $promo->value }}%
                                        @else
                                            Rp {{ number_format($promo->value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        @if($promo->valid_from) {{ \Carbon\Carbon::parse($promo->valid_from)->format('d M y H:i') }} @else Selalu @endif
                                        <br>
                                        s/d
                                        <br>
                                        @if($promo->valid_until) {{ \Carbon\Carbon::parse($promo->valid_until)->format('d M y H:i') }} @else Selalu @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($promo->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $promo->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="delete({{ $promo->id }})" class="text-red-600 hover:text-red-900" onclick="confirm('Yakin ingin menghapus promo ini?') || event.stopImmediatePropagation()">Hapus</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada promo yang dibuat.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $promotions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
