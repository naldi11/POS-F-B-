<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;

class Report extends Component
{
    public $filter = 'today';
    public $startDate;
    public $endDate;
    public $selectedDate = null;
    public $totalRevenue = 0;
    public $totalOrders = 0;
    public $orders = [];
    public $dailyBreakdown = [];
    public $periodSummary = [
        'totalOrders' => 0,
        'totalRevenue' => 0,
    ];
    public $sortOrder = 'desc';
    public $chartLabels = [];
    public $chartData = [];
    public $storeName = 'RUMPO CAFE';
    public $storeAddress = '';

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        
        $settings = Setting::pluck('value', 'key')->toArray();
        $this->storeName = $settings['receipt_store_name'] ?? 'RUMPO CAFE';
        
        $addressParts = [];
        if (!empty($settings['receipt_address'])) $addressParts[] = $settings['receipt_address'];
        if (!empty($settings['receipt_phone'])) $addressParts[] = 'Telp: ' . $settings['receipt_phone'];
        $this->storeAddress = implode(' - ', $addressParts);

        $this->generateReport();
    }

    public function updatedFilter()
    {
        $this->selectedDate = null;

        if ($this->filter === 'today') {
            $this->startDate = Carbon::today()->format('Y-m-d');
            $this->endDate = Carbon::today()->format('Y-m-d');
        } elseif ($this->filter === 'this_week') {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($this->filter === 'this_month') {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($this->filter === 'this_year') {
            $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        } elseif ($this->filter === 'all') {
            $firstOrderDate = Order::where('status', 'completed')->oldest('created_at')->value('created_at');
            $this->startDate = $firstOrderDate ? Carbon::parse($firstOrderDate)->format('Y-m-d') : Carbon::today()->subMonths(1)->format('Y-m-d');
            $this->endDate = Carbon::today()->format('Y-m-d');
        }

        $this->generateReport();
    }

    public function updatedSortOrder()
    {
        $this->generateReport();
    }

    public function updatedStartDate()
    {
        $this->filter = 'custom';
        $this->selectedDate = null;
        $this->generateReport();
    }

    public function updatedEndDate()
    {
        $this->filter = 'custom';
        $this->selectedDate = null;
        $this->generateReport();
    }

    public function selectDate($date)
    {
        if ($this->selectedDate === $date) {
            $this->selectedDate = null;
        } else {
            $this->selectedDate = $date;
        }
        $this->generateReport();
    }

    public function resetSelectedDate()
    {
        $this->selectedDate = null;
        $this->generateReport();
    }

    public function generateReport()
    {
        // 1. Query for the overall period
        $basePeriodQuery = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        // Period Totals
        $this->periodSummary = [
            'totalOrders' => (clone $basePeriodQuery)->count(),
            'totalRevenue' => (clone $basePeriodQuery)->sum('total_amount') ?? 0,
        ];

        // All period orders for breakdown & chart
        $allPeriodOrders = (clone $basePeriodQuery)
            ->with(['table', 'orderDetails.menu', 'orderDetails.bundle'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Build Daily Breakdown (Folder / Day tabs)
        $groupedByDate = $allPeriodOrders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d');
        });

        $this->dailyBreakdown = [];
        foreach ($groupedByDate as $dateKey => $dayOrders) {
            $parsedDate = Carbon::parse($dateKey)->locale('id');
            $this->dailyBreakdown[] = [
                'date' => $dateKey,
                'day_name' => $parsedDate->isoFormat('dddd'),
                'formatted_date' => $parsedDate->isoFormat('D MMMM Y'),
                'short_date' => $parsedDate->isoFormat('D MMM Y'),
                'total_orders' => $dayOrders->count(),
                'total_revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        // 3. Chart Data (Grouped chronologically ascending)
        $groupedForChart = $allPeriodOrders->sortBy('created_at')->groupBy(function($item) {
            return $item->created_at->format('d M Y');
        });

        $this->chartLabels = $groupedForChart->keys()->toArray();
        $this->chartData = $groupedForChart->map(function($row) {
            return $row->sum('total_amount');
        })->values()->toArray();

        // 4. Determine Active Display Query (Single Day vs Full Period)
        if ($this->selectedDate) {
            $activeQuery = Order::where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::parse($this->selectedDate)->startOfDay(),
                    Carbon::parse($this->selectedDate)->endOfDay()
                ]);
        } else {
            $activeQuery = clone $basePeriodQuery;
        }

        $this->totalOrders = (clone $activeQuery)->count();
        $this->totalRevenue = (clone $activeQuery)->sum('total_amount') ?? 0;
        
        $this->orders = (clone $activeQuery)
            ->with(['table', 'orderDetails.menu', 'orderDetails.bundle'])
            ->orderBy('created_at', $this->sortOrder)
            ->get();

        $this->dispatch('updateChart', labels: $this->chartLabels, data: $this->chartData);
    }

    public function exportCsv()
    {
        $dateSuffix = $this->selectedDate ? $this->selectedDate : $this->startDate . '_sd_' . $this->endDate;
        $filename = 'Laporan_Penjualan_' . $dateSuffix . '.xls';

        if ($this->selectedDate) {
            $exportQuery = Order::where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::parse($this->selectedDate)->startOfDay(),
                    Carbon::parse($this->selectedDate)->endOfDay()
                ]);
        } else {
            $exportQuery = Order::where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
        }

        $ordersToExport = $exportQuery
            ->with(['table', 'orderDetails.menu', 'orderDetails.bundle', 'promotion'])
            ->orderBy('created_at', $this->sortOrder)
            ->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $storeName = $this->storeName;
        $storeAddress = $this->storeAddress;
        $selectedDate = $this->selectedDate;
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $totalOrdersCount = $ordersToExport->count();
        $totalRevenueSum = $ordersToExport->sum('total_amount');

        $callback = function() use ($ordersToExport, $storeName, $storeAddress, $selectedDate, $startDate, $endDate, $totalOrdersCount, $totalRevenueSum) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Penjualan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1f2937; }';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo '.header-title { font-size: 16pt; font-weight: bold; color: #c2410c; }';
            echo '.header-subtitle { font-size: 10pt; color: #4b5563; }';
            echo '.meta-table td { padding: 4px 8px; border: none; font-size: 10pt; }';
            echo '.meta-label { font-weight: bold; width: 140px; }';
            echo 'th { background-color: #ea580c; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #c2410c; padding: 10px 8px; font-size: 10.5pt; }';
            echo 'td { border: 1px solid #d1d5db; padding: 6px 8px; font-size: 10pt; vertical-align: middle; }';
            echo '.text-center { text-align: center; }';
            echo '.text-right { text-align: right; }';
            echo '.font-bold { font-weight: bold; }';
            echo '.total-row td { background-color: #ffedd5; font-weight: bold; border-top: 2px solid #ea580c; border-bottom: 2px solid #ea580c; font-size: 11pt; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';

            // Top Store Header
            echo '<table>';
            echo '<tr><td colspan="8" class="header-title">' . htmlspecialchars($storeName) . '</td></tr>';
            if ($storeAddress) {
                echo '<tr><td colspan="8" class="header-subtitle">' . htmlspecialchars($storeAddress) . '</td></tr>';
            }
            echo '<tr><td colspan="8" style="font-size: 13pt; font-weight: bold; padding-top: 10px; color: #111827;">LAPORAN PENJUALAN</td></tr>';
            
            if ($selectedDate) {
                echo '<tr><td colspan="2" class="meta-label">Filter Tanggal:</td><td colspan="6">' . htmlspecialchars(Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y')) . '</td></tr>';
            } else {
                echo '<tr><td colspan="2" class="meta-label">Periode Laporan:</td><td colspan="6">' . htmlspecialchars(Carbon::parse($startDate)->locale('id')->isoFormat('D MMM Y') . ' s/d ' . Carbon::parse($endDate)->locale('id')->isoFormat('D MMM Y')) . '</td></tr>';
            }
            echo '<tr><td colspan="2" class="meta-label">Total Pesanan:</td><td colspan="6">' . $totalOrdersCount . ' Pesanan</td></tr>';
            echo '<tr><td colspan="2" class="meta-label">Total Pendapatan:</td><td colspan="6">Rp ' . number_format($totalRevenueSum, 0, ',', '.') . '</td></tr>';
            echo '<tr><td colspan="2" class="meta-label">Tanggal Unduh:</td><td colspan="6">' . htmlspecialchars(Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm')) . ' WIB</td></tr>';
            echo '<tr><td colspan="8" style="height: 15px;"></td></tr>';
            echo '</table>';

            // Data Table
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th style="width: 90px;">No. Order</th>';
            echo '<th style="width: 140px;">Waktu</th>';
            echo '<th style="width: 70px;">Meja</th>';
            echo '<th style="width: 130px;">Nama Pemesan</th>';
            echo '<th style="width: 320px;">Rincian Menu & Qty</th>';
            echo '<th style="width: 130px;">Diskon / Promo</th>';
            echo '<th style="width: 120px;">Total (Rp)</th>';
            echo '<th style="width: 90px;">Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($ordersToExport as $order) {
                $itemDetails = [];
                foreach ($order->orderDetails as $detail) {
                    $name = $detail->menu ? $detail->menu->name : ($detail->bundle ? $detail->bundle->name : 'Menu');
                    $itemDetails[] = htmlspecialchars($name) . ' (x' . $detail->quantity . ')';
                }
                $itemsStr = implode('<br/>', $itemDetails);

                $promoStr = '-';
                if ($order->discount_amount > 0) {
                    $promoStr = 'Rp ' . number_format($order->discount_amount, 0, ',', '.');
                    if ($order->promotion) {
                        $promoStr .= '<br/><small>' . htmlspecialchars($order->promotion->name) . '</small>';
                    }
                }

                echo '<tr>';
                echo '<td class="text-center font-bold">#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '</td>';
                echo '<td class="text-center">' . $order->created_at->format('d/m/Y H:i') . '</td>';
                echo '<td class="text-center font-bold">' . htmlspecialchars($order->table ? $order->table->table_number : '-') . '</td>';
                echo '<td>' . htmlspecialchars($order->customer_name ?: '-') . '</td>';
                echo '<td>' . ($itemsStr ?: '-') . '</td>';
                echo '<td class="text-center">' . $promoStr . '</td>';
                echo '<td class="text-right font-bold">Rp ' . number_format($order->total_amount, 0, ',', '.') . '</td>';
                echo '<td class="text-center" style="color: #059669; font-weight: bold;">Selesai</td>';
                echo '</tr>';
            }

            // Total Summary Row
            echo '<tr class="total-row">';
            echo '<td colspan="4" class="text-right">TOTAL KESELURUHAN:</td>';
            echo '<td colspan="2" class="text-center">' . $totalOrdersCount . ' Pesanan</td>';
            echo '<td class="text-right">Rp ' . number_format($totalRevenueSum, 0, ',', '.') . '</td>';
            echo '<td></td>';
            echo '</tr>';

            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function render()
    {
        return view('livewire.admin.report');
    }
}
