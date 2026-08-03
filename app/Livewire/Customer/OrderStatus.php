<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Session;
use Livewire\WithFileUploads;

#[Layout('layouts.customer')]
class OrderStatus extends Component
{
    use WithFileUploads;

    public $order;
    public $payment_proof;

    public function mount($id)
    {
        $this->order = Order::with(['orderDetails.menu', 'orderDetails.bundle', 'payment', 'table'])->findOrFail($id);
    }

    public function reuploadPayment()
    {
        $this->validate([
            'payment_proof' => 'required|image|max:51200',
        ]);

        $proofPath = $this->payment_proof->store('payments', 'public');

        if ($this->order->payment) {
            $this->order->payment->update([
                'proof_image' => $proofPath,
                'status' => 'pending'
            ]);
        } else {
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

    /**
     * Pelanggan menekan tombol "Selesai" → hapus session meja & kembali ke scan QR.
     */
    public function leaveTable()
    {
        Session::forget('table_id');
        Session::forget('table_number');

        return $this->redirect(route('welcome'), navigate: true);
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
