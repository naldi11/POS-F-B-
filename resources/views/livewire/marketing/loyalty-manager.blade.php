<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Loyalty Poin</h2>
            <p class="text-gray-500 text-sm mt-1">Atur perolehan dan nilai tukar poin untuk pelanggan.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Settings Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-1 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Aturan Poin</h3>
            
            <form wire:submit.prevent="saveSettings" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poin Didapat per Rp 1.000 Belanja</label>
                    <div class="relative">
                        <input type="number" wire:model="points_per_1000" class="w-full pl-4 pr-12 py-2 rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition" step="0.1" min="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 font-semibold text-sm">
                            Poin
                        </div>
                    </div>
                    @error('points_per_1000') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Misal: Jika diisi 1, maka belanja Rp 50.000 dapat 50 Poin.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Tukar 1 Poin (Saat Redeem)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-bold text-sm">
                            Rp
                        </div>
                        <input type="number" wire:model="point_value" class="w-full pl-9 py-2 rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 transition" min="0">
                    </div>
                    @error('point_value') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Misal: Jika diisi 10, maka menukar 100 Poin memberi diskon Rp 1.000.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                        <svg wire:loading.remove wire:target="saveSettings" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg wire:loading wire:target="saveSettings" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Simpan Aturan</span>
                    </button>
                </div>
            </form>
            
            <div class="mt-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                <h4 class="font-bold text-sm text-blue-800 mb-1">Simulasi:</h4>
                <p class="text-xs text-blue-700 leading-relaxed">
                    Pelanggan belanja <span class="font-bold">Rp 100.000</span>.<br>
                    Mendapat: <span class="font-bold">{{ 100 * ($points_per_1000 ?: 0) }} Poin</span>.<br>
                    Nilai tukar poin tersebut: <span class="font-bold">Rp {{ number_format((100 * ($points_per_1000 ?: 0)) * ($point_value ?: 0), 0, ',', '.') }}</span>.
                </p>
            </div>
        </div>

        <!-- Customer List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 lg:col-span-2 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-bold text-gray-800">Daftar Poin Member</h3>
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live="search" placeholder="Cari nama / no HP..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="py-3 px-6 font-semibold text-gray-600 text-xs uppercase tracking-wider">Pelanggan</th>
                            <th class="py-3 px-6 font-semibold text-gray-600 text-xs uppercase tracking-wider">No. HP / WA</th>
                            <th class="py-3 px-6 font-semibold text-gray-600 text-xs uppercase tracking-wider text-right">Saldo Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($customer->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">{{ $customer->name ?? 'Tanpa Nama' }}</p>
                                            <p class="text-xs text-gray-500">Bergabung: {{ $customer->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600 font-mono">
                                    {{ $customer->phone }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold bg-green-50 text-green-700 border border-green-200">
                                        {{ number_format($customer->points, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p class="text-sm">Belum ada data member/pelanggan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($customers->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
