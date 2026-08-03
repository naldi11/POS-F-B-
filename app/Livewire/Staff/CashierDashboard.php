<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Order;

class CashierDashboard extends Component
{
    public $tab = 'active'; // 'active', 'completed'
    public $dateFilter = 'all'; // 'today', 'week', 'month', 'all', 'custom'
    public $customDate = '';
    public $search = '';

    public function mount()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'cashier') {
            abort(403, 'Unauthorized action.');
        }
    }

    #[\Livewire\Attributes\Computed]
    public function orders()
    {
        $query = Order::with(['table', 'payment', 'orderDetails.menu']);

        // Tab Filter
        if ($this->tab === 'completed') {
            $query->where('status', 'completed');
        } else {
            $query->where('status', '!=', 'completed');
        }

        // Date Filter
        $now = \Carbon\Carbon::now();
        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($this->dateFilter === 'week') {
            $query->whereBetween('created_at', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
        } elseif ($this->dateFilter === 'month') {
            $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
        } elseif ($this->dateFilter === 'custom' && $this->customDate) {
            $query->whereDate('created_at', $this->customDate);
        }

        // Search Filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    #[On('echo:orders,NewOrder')]
    #[On('echo:orders,OrderUpdated')]
    public function refreshOrders()
    {
        unset($this->orders);
        $this->dispatch('play-notification');
    }

    public function verifyPayment($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order && $order->payment) {
            $order->payment->update(['status' => $status]);
            
            if ($status === 'verified') {
                $order->update(['status' => 'verified']);
            } else {
                $order->update(['status' => 'waiting_payment']);
            }
            
            \App\Events\OrderUpdated::dispatch($order);
            unset($this->orders);
        }
    }

    private function syncTableStatus($tableId)
    {
        if (!$tableId) return;
        $table = \App\Models\Table::find($tableId);
        if (!$table) return;

        $hasActiveOrders = Order::where('table_id', $tableId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        $table->update([
            'status' => $hasActiveOrders ? 'occupied' : 'available'
        ]);
    }

    public function completeOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => 'completed']);
            $this->syncTableStatus($order->table_id);
            \App\Events\OrderUpdated::dispatch($order);
            unset($this->orders);
        }
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => $status]);
            $this->syncTableStatus($order->table_id);
            \App\Events\OrderUpdated::dispatch($order);
            unset($this->orders);
        }
    }

    public function render()
    {
        return view('livewire.staff.cashier-dashboard');
    }
}
