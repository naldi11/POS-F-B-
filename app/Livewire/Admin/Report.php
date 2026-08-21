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
        $filename = 'Laporan_Penjualan_' . ($this->selectedDate ? $this->selectedDate : $this->startDate . '_sd_' . $this->endDate) . '.csv';

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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $storeName = $this->storeName;
        $selectedDate = $this->selectedDate;
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        $callback = function() use ($ordersToExport, $storeName, $selectedDate, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens indonesian characters and currencies correctly
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [$storeName, 'LAPORAN PENJUALAN']);
            if ($selectedDate) {
                fputcsv($file, ['Filter Tanggal', Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y')]);
            } else {
                fputcsv($file, ['Periode Laporan', Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y')]);
            }
            fputcsv($file, ['Total Pesanan', $ordersToExport->count() . ' Pesanan']);
            fputcsv($file, ['Total Pendapatan', 'Rp ' . number_format($ordersToExport->sum('total_amount'), 0, ',', '.')]);
            fputcsv($file, ['Tanggal Unduh', Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB']);
            fputcsv($file, []); // Empty line

            fputcsv($file, [
                'No. Order',
                'Waktu Pesanan',
                'No. Meja',
                'Nama Pemesan',
                'Rincian Menu & Qty',
                'Diskon / Promo',
                'Total Nominal (Rp)',
                'Status'
            ]);

            foreach ($ordersToExport as $order) {
                $itemDetails = [];
                foreach ($order->orderDetails as $detail) {
                    $name = $detail->menu ? $detail->menu->name : ($detail->bundle ? $detail->bundle->name : 'Menu');
                    $itemDetails[] = $name . ' (x' . $detail->quantity . ')';
                }
                $itemsStr = implode(', ', $itemDetails);

                $promoStr = '-';
                if ($order->discount_amount > 0) {
                    $promoStr = 'Rp ' . number_format($order->discount_amount, 0, ',', '.');
                    if ($order->promotion) {
                        $promoStr .= ' (' . $order->promotion->name . ')';
                    }
                }

                fputcsv($file, [
                    '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    $order->created_at->format('d/m/Y H:i'),
                    $order->table ? $order->table->table_number : '-',
                    $order->customer_name ?: '-',
                    $itemsStr ?: '-',
                    $promoStr,
                    $order->total_amount,
                    'Selesai'
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL AKHIR', '', $ordersToExport->sum('total_amount'), '']);

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function render()
    {
        return view('livewire.admin.report');
    }
}
