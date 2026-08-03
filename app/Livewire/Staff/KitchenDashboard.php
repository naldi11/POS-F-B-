<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Order;

class KitchenDashboard extends Component
{
    public $orders = [];

    public function mount()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'kitchen') {
            abort(403, 'Unauthorized action.');
        }
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Only load orders that are 'processing'
        $this->orders = Order::with(['table', 'orderDetails.menu'])
            ->where('status', 'processing')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    #[On('echo:orders,OrderUpdated')]
    public function refreshOrders()
    {
        $this->loadOrders();
        $this->dispatch('play-notification');
    }

    public function markAsReady($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => 'completed']);

            $hasActiveOrders = Order::where('table_id', $order->table_id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->exists();

            if ($order->table) {
                $order->table->update([
                    'status' => $hasActiveOrders ? 'occupied' : 'available'
                ]);
            }

            \App\Events\OrderUpdated::dispatch($order);
            $this->loadOrders();
        }
    }

    public function render()
    {
        return view('livewire.staff.kitchen-dashboard');
    }
}
