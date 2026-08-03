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
        $this->orders = Order::with(['table', 'orderDetails.menu', 'orderDetails.bundle'])
            ->whereIn('status', ['cooking', 'verified'])
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
        if ($order && $order->status !== 'completed') {
            $order->update(['status' => 'completed']);
            
            if ($order->customer_id && $order->points_earned > 0) {
                $customer = \App\Models\Customer::find($order->customer_id);
                if ($customer) {
                    $customer->increment('points', $order->points_earned);
                }
            }

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
