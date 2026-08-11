<div class="py-12">
    <style>
        @media print {
            aside, header, nav, .print-hidden { display: none !important; }
            body { background-color: #fff !important; margin: 0; padding: 0; }
            .bg-white { box-shadow: none !important; border: none !important; background: #fff !important; }
            .grid { display: none !important; }
            .rounded-full { display: none !important; }
            * { color: #000 !important; }
            
            .print-table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; margin-top: 20px !important; }
            .print-table th { border: 1px solid #000 !important; padding: 10px !important; text-align: left !important; font-weight: bold !important; background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; text-transform: uppercase; font-size: 11px; }
            .print-table td { border: 1px solid #000 !important; padding: 8px !important; font-size: 11px; }
            
            .py-12 { padding: 0 !important; }
            .max-w-7xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            
            .print-summary { display: flex !important; justify-content: flex-end; margin-top: 20px; }
            .print-summary table { width: 300px !important; border: none !important; }
            .print-summary th, .print-summary td { border: none !important; padding: 5px 10px !important; font-size: 12px; text-transform: none; background: transparent !important; }
            .print-summary th { text-align: left !important; }
            .print-summary td { text-align: right !important; font-weight: bold; }
            
            .print-signature { display: block !important; margin-top: 50px; float: right; width: 250px; text-align: center; }
            .print-signature p { margin: 5px 0; font-size: 12px; }
            .print-signature .sign-space { height: 80px; }
            .print-signature .sign-name { font-weight: bold; text-decoration: underline; }
            
            .chart-container { display: none !important; }
        }
        @media screen {
            .print-only { display: none !important; }
        }
    </style>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="print-only" style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 25px;">
            <div style="text-align: left;">
                <h1 style="font-size: 26px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">{{ $storeName }}</h1>
                @if($storeAddress)
                    <p style="font-size: 12px; margin: 5px 0 0 0; color: #444;">{{ $storeAddress }}</p>
                @endif
            </div>
            <div style="text-align: right;">
                <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #222; text-transform: uppercase;">Laporan Penjualan</h2>
                <p style="font-size: 12px; margin: 5px 0 0 0; color: #444;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 print-hidden">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                <p class="text-sm text-gray-500">Ringkasan pendapatan dari pesanan yang selesai</p>
            </div>
            
            <div class="flex flex-wrap gap-2 items-center">
                <select wire:model.live="sortOrder" class="min-w-[160px] pr-8 bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                    <option value="desc">Urutkan: Terbaru</option>
                    <option value="asc">Urutkan: Terlama</option>
                </select>
                <select wire:model.live="filter" class="min-w-[150px] pr-8 bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
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

        <!-- Chart Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 chart-container print-hidden" wire:ignore
             x-data="{
                chart: null,
                init() {
                    const initChart = () => {
                        if (typeof Chart === 'undefined') {
                            setTimeout(initChart, 100);
                            return;
                        }
                        const ctx = this.$refs.canvas;
                        if (!ctx) return;
                        
                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($chartLabels),
                                datasets: [{
                                    label: 'Pendapatan (Rp)',
                                    data: @json($chartData),
                                    borderColor: '#f97316',
                                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    };
                    initChart();
                    
                    Livewire.on('updateChart', (data) => {
                        if(this.chart) {
                            this.chart.data.labels = data[0].labels;
                            this.chart.data.datasets[0].data = data[0].data;
                            this.chart.update();
                        }
                    });
                }
             }">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Pendapatan</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-0 print:border-none">
            <div class="p-0 text-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 print-table">
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

        <!-- Print Summary Table -->
        <div class="print-summary print-only">
            <table>
                <tr>
                    <th>Total Pesanan:</th>
                    <td>{{ $totalOrders }} Pesanan</td>
                </tr>
                <tr>
                    <th>Total Pendapatan:</th>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Print Signature Block -->
        <div class="print-signature print-only">
            <p>Jakarta, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <div class="sign-space"></div>
            <p class="sign-name">Manajer Rumpo Cafe</p>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets
