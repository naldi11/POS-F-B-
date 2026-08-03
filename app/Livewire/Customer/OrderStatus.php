<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use Livewire\Attributes\On;

use Livewire\WithFileUploads;

#[Layout('layouts.customer')]
class OrderStatus extends Component
{
    use WithFileUploads;

    public $order;
    public $payment_proof;

    public function mount($id)
    {
        $this->order = Order::with(['orderDetails.menu', 'payment', 'table'])->findOrFail($id);
    }

    public function reuploadPayment()
    {
        $this->validate([
            'payment_proof' => 'required|image|max:51200', // Max 50MB
        ]);

        $proofPath = $this->payment_proof->store('payments', 'public');

        if ($this->order->payment) {
            $this->order->payment->update([
                'proof_image' => $proofPath,
                'status' => 'pending'
            ]);
        } else {
            // Just in case there is no payment record yet (though there should be)
            \App\Models\Payment::create([
                'order_id' => $this->order->id,
                'proof_image' => $proofPath,
                'status' => 'pending'
            ]);
        }

        $this->order->update([
            'status' => 'waiting_verification'
        ]);

        \App\Events\OrderUpdated::dispatch($this->order);
        
        $this->payment_proof = null;
        $this->order->refresh();
        session()->flash('message', 'Bukti pembayaran berhasil diunggah ulang! Menunggu verifikasi kasir.');
    }

    #[On('echo:orders,OrderUpdated')]
    public function refreshOrder()
    {
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.customer.order-status');
    }
}
