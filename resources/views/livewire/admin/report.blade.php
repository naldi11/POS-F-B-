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
            .print-summary table { width: 320px !important; border: none !important; }
            .print-summary th, .print-summary td { border: none !important; padding: 5px 10px !important; font-size: 12px; text-transform: none; background: transparent !important; }
            .print-summary th { text-align: left !important; }
            .print-summary td { text-align: right !important; font-weight: bold; }
            
            .print-signature { display: block !important; margin-top: 40px; float: right; width: 250px; text-align: center; }
            .print-signature p { margin: 5px 0; font-size: 12px; }
            .print-signature .sign-space { height: 70px; }
            .print-signature .sign-name { font-weight: bold; text-decoration: underline; }
            
            .chart-container { display: none !important; }
        }
        @media screen {
            .print-only { display: none !important; }
        }
    </style>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Print Header -->
        <div class="print-only" style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 25px;">
            <div style="text-align: left;">
                <h1 style="font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">{{ $storeName }}</h1>
                @if($storeAddress)
                    <p style="font-size: 11px; margin: 4px 0 0 0; color: #444;">{{ $storeAddress }}</p>
                @endif
            </div>
            <div style="text-align: right;">
                <h2 style="font-size: 18px; font-weight: bold; margin: 0; color: #111; text-transform: uppercase;">
                    {{ $selectedDate ? 'Laporan Penjualan Harian' : 'Laporan Penjualan' }}
                </h2>
                <p style="font-size: 11px; margin: 4px 0 0 0; color: #444;">
                    @if($selectedDate)
                        Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    @else
                        Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMM Y') }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Screen Top Header & Filters -->
        <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 print-hidden">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                <p class="text-sm text-gray-500">Ringkasan pendapatan dan transaksi dari pesanan yang selesai</p>
            </div>
            
            <div class="flex flex-wrap gap-2 items-center">
                <!-- Sort Order -->
                <select wire:model.live="sortOrder" class="min-w-[140px] pr-8 bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                    <option value="desc">Urutkan: Terbaru</option>
                    <option value="asc">Urutkan: Terlama</option>
                </select>

                <!-- Filter Period -->
                <select wire:model.live="filter" class="min-w-[160px] pr-8 bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2 shadow-sm transition">
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="this_year">Tahun Ini</option>
                    <option value="all">Keseluruhan (Semua)</option>
                    <option value="custom">Kustom Tanggal</option>
                </select>
                
                @if($filter === 'custom')
                    <div class="flex items-center gap-1.5 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                        <input type="date" wire:model.live="startDate" class="bg-transparent border-0 text-gray-700 text-sm focus:ring-0 p-1">
                        <span class="text-gray-400 font-medium">-</span>
                        <input type="date" wire:model.live="endDate" class="bg-transparent border-0 text-gray-700 text-sm focus:ring-0 p-1">
                    </div>
                @endif
                
                <!-- Download Excel/CSV Button -->
                <button wire:click="exportCsv" wire:loading.attr="disabled" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3.5 py-2 rounded-lg shadow-sm text-sm font-medium transition flex items-center space-x-1.5 cursor-pointer disabled:opacity-50" title="Unduh data laporan dalam format CSV / Excel">
                    <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <svg wire:loading wire:target="exportCsv" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Download Excel</span>
                </button>

                <!-- Print Report Button -->
                <button onclick="window.print()" class="bg-orange-500 hover:bg-orange-600 active:scale-95 text-white px-3.5 py-2 rounded-lg shadow-sm text-sm font-medium transition flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- Metrics Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="bg-orange-50 p-4 rounded-full mr-4 print-hidden">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        Total Pendapatan {{ $selectedDate ? '(' . \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('D MMM Y') . ')' : '' }}
                    </p>
                    <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="bg-green-50 p-4 rounded-full mr-4 print-hidden">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        Total Pesanan Selesai {{ $selectedDate ? '(' . \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('D MMM Y') . ')' : '' }}
                    </p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $totalOrders }} Pesanan</h3>
                </div>
            </div>
        </div>

        <!-- Active Date Filter Alert Banner -->
        @if($selectedDate)
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs print-hidden animate-fadeIn">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-orange-500 text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-orange-950">
                            Menampilkan Khusus Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div class="text-xs text-orange-700">
                            Ditemukan <strong>{{ $totalOrders }} pesanan</strong> dengan omzet <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong> pada hari ini.
                        </div>
                    </div>
                </div>
                <button wire:click="resetSelectedDate" class="inline-flex items-center justify-center space-x-1.5 px-3 py-1.5 bg-white hover:bg-orange-100 text-orange-800 border border-orange-300 rounded-lg text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Tampilkan Semua Tanggal ({{ $periodSummary['totalOrders'] }} Pesanan)</span>
                </button>
            </div>
        @endif

        <!-- Folder Hari / Kalender Grid Section (Multi-Day Filters) -->
        @if($filter !== 'today' && count($dailyBreakdown) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 print-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span>📁 Folder & Kalender Hari</span>
                            <span class="text-xs font-normal bg-orange-100 text-orange-800 px-2.5 py-0.5 rounded-full">
                                {{ count($dailyBreakdown) }} Hari dengan Transaksi
                            </span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Klik salah satu folder tanggal di bawah untuk melihat rincian transaksi di hari tersebut:
                        </p>
                    </div>

                    @if($selectedDate)
                        <button wire:click="resetSelectedDate" class="text-xs text-orange-600 hover:text-orange-700 font-semibold underline cursor-pointer self-start sm:self-auto">
                            Tampilkan Keseluruhan Hari
                        </button>
                    @endif
                </div>

                <!-- Folder Day Cards Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 pt-1">
                    <!-- All Days Tab Card -->
                    <div wire:click="resetSelectedDate" 
                         class="cursor-pointer rounded-xl p-3 border transition-all duration-200 flex flex-col justify-between {{ $selectedDate === null ? 'bg-orange-50 border-orange-400 ring-2 ring-orange-400/20 shadow-sm' : 'bg-gray-50/70 hover:bg-gray-100 border-gray-200' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-lg">📂</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $selectedDate === null ? 'text-orange-700 bg-orange-200/70' : 'text-gray-600 bg-gray-200' }} px-1.5 py-0.5 rounded">
                                Semua
                            </span>
                        </div>
                        <div>
                            <div class="text-xs font-bold {{ $selectedDate === null ? 'text-orange-950' : 'text-gray-900' }} truncate">
                                Semua Tanggal
                            </div>
                            <div class="text-[11px] font-semibold text-gray-500 mt-0.5">
                                {{ $periodSummary['totalOrders'] }} Pesanan
                            </div>
                            <div class="text-xs font-bold {{ $selectedDate === null ? 'text-orange-600' : 'text-gray-700' }} mt-1">
                                Rp {{ number_format($periodSummary['totalRevenue'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Individual Daily Cards -->
                    @foreach($dailyBreakdown as $day)
                        @php
                            $isSelected = $selectedDate === $day['date'];
                        @endphp
                        <div wire:click="selectDate('{{ $day['date'] }}')" 
                             class="cursor-pointer rounded-xl p-3 border transition-all duration-200 flex flex-col justify-between {{ $isSelected ? 'bg-orange-500 text-white border-orange-600 shadow-md ring-2 ring-orange-500/30 scale-[1.02]' : 'bg-white hover:border-orange-300 hover:bg-orange-50/30 border-gray-200' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base">{{ $isSelected ? '📅' : '📁' }}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $isSelected ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $day['day_name'] }}
                                </span>
                            </div>
                            <div>
                                <div class="text-xs font-bold {{ $isSelected ? 'text-white' : 'text-gray-900' }} truncate" title="{{ $day['formatted_date'] }}">
                                    {{ $day['short_date'] }}
                                </div>
                                <div class="text-[11px] font-medium {{ $isSelected ? 'text-orange-100' : 'text-gray-500' }} mt-0.5">
                                    {{ $day['total_orders'] }} Pesanan
                                </div>
                                <div class="text-xs font-bold {{ $isSelected ? 'text-white' : 'text-orange-600' }} mt-1 truncate">
                                    Rp {{ number_format($day['total_revenue'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Chart Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 chart-container print-hidden" wire:ignore>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Pendapatan</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="revenueChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>

@script
<script>
    let chartInstance = null;
    const initChart = () => {
        if (typeof Chart === 'undefined') {
            setTimeout(initChart, 100);
            return;
        }
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        
        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: $wire.chartLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: $wire.chartData,
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
    
    $wire.on('updateChart', (event) => {
        if(chartInstance) {
            let payload = Array.isArray(event) ? event[0] : event;
            if (payload && payload.labels) {
                chartInstance.data.labels = payload.labels;
                chartInstance.data.datasets[0].data = payload.data;
                chartInstance.update();
            }
        }
    });
</script>
@endscript

        <!-- Order Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print:border-none">
            <div class="p-0 text-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 print-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3.5">Order ID</th>
                                <th scope="col" class="px-6 py-3.5">Waktu</th>
                                <th scope="col" class="px-6 py-3.5">Meja</th>
                                <th scope="col" class="px-6 py-3.5">Pemesan</th>
                                <th scope="col" class="px-6 py-3.5">Rincian Menu</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                <tr class="bg-white hover:bg-gray-50/60 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $order->table ? $order->table->table_number : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $order->customer_name ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        <div class="text-xs space-y-1">
                                            @forelse($order->orderDetails as $detail)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                                    <span class="font-medium text-gray-800">
                                                        {{ $detail->menu ? $detail->menu->name : ($detail->bundle ? $detail->bundle->name : 'Menu') }}
                                                    </span>
                                                    <span class="text-gray-500 font-semibold">(x{{ $detail->quantity }})</span>
                                                </div>
                                            @empty
                                                <span class="text-gray-400 italic">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="font-semibold text-gray-600">Tidak ada data penjualan pada filter yang dipilih.</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba ubah rentang tanggal atau pilih filter hari lain.</p>
                                        </div>
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
                    <th>Total Pesanan Selesai:</th>
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
            <p>Medan, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Mengetahui,</p>
            <div class="sign-space"></div>
            <p class="sign-name">Manajer Rumpo Cafe</p>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

