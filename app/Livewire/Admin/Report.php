<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;

class Report extends Component
{
    public $filter = 'today';
    public $startDate;
    public $endDate;
    public $totalRevenue = 0;
    public $totalOrders = 0;
    public $orders = [];
    public $sortOrder = 'desc';
    public $chartLabels = [];
    public $chartData = [];

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->generateReport();
    }

    public function updatedFilter()
    {
        if ($this->filter === 'today') {
            $this->startDate = Carbon::today()->format('Y-m-d');
            $this->endDate = Carbon::today()->format('Y-m-d');
        } elseif ($this->filter === 'this_week') {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($this->filter === 'this_month') {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
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
        $this->generateReport();
    }

    public function updatedEndDate()
    {
        $this->filter = 'custom';
        $this->generateReport();
    }

    public function generateReport()
    {
        $query = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        $this->totalOrders = $query->count();
        $this->totalRevenue = $query->sum('total_amount');
        
        $this->orders = $query->with('table')->orderBy('created_at', $this->sortOrder)->get();

        $allOrdersForChart = clone $query;
        $grouped = $allOrdersForChart->orderBy('created_at', 'asc')->get()->groupBy(function($item) {
            return $item->created_at->format('d M Y');
        });

        $this->chartLabels = $grouped->keys()->toArray();
        $this->chartData = $grouped->map(function($row) {
            return $row->sum('total_amount');
        })->values()->toArray();

        $this->dispatch('updateChart', labels: $this->chartLabels, data: $this->chartData);
    }

    public function render()
    {
        return view('livewire.admin.report');
    }
}
