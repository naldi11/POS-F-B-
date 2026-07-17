<div class="py-12">
    <style>
        @media print {
            aside, header, nav, .print-hidden { display: none !important; }
            body, .bg-\[\#f1f5f9\] { background-color: #fff !important; }
            .bg-white { box-shadow: none !important; }
            .grid { display: flex !important; flex-direction: row !important; gap: 20px !important; margin-bottom: 20px !important; }
            .grid > div { border: 1px solid #000 !important; padding: 10px !important; flex: 1 !important; }
            .rounded-full { display: none !important; }
            * { color: #000 !important; }
            table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
            th, td { border: 1px solid #000 !important; padding: 8px !important; background: transparent !important; }
            .py-12 { padding: 0 !important; }
            .max-w-7xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                <p class="text-sm text-gray-500">Ringkasan pendapatan dari pesanan yang selesai</p>
            </div>
            
            <div class="flex space-x-2 print-hidden">
                <select wire:model.live="filter" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">Kustom</option>
                </select>
                
                @if($filter === 'custom')
                    <input type="date" wire:model.live="startDate" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                    <span class="self-center text-gray-500">-</span>
                    <input type="date" wire:model.live="endDate" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                @endif
                
                <button onclick="window.print()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="bg-orange-50 p-4 rounded-full mr-4 print-hidden">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pendapatan</p>
                    <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="bg-green-50 p-4 rounded-full mr-4 print-hidden">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pesanan Selesai</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $totalOrders }} Pesanan</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-0 print:border-none">
            <div class="p-0 text-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Order ID</th>
                                <th scope="col" class="px-6 py-3">Waktu</th>
                                <th scope="col" class="px-6 py-3">Meja</th>
                                <th scope="col" class="px-6 py-3">Pemesan</th>
                                <th scope="col" class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->table->table_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada data penjualan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
