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

    public function generateReport()
    {
        $query = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        $this->totalOrders = $query->count();
        $this->totalRevenue = $query->sum('total_amount');
        
        $this->orders = $query->with('table')->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.admin.report');
    }
}
