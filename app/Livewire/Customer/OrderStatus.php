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
     * Pelanggan mengonfirmasi bahwa pesanan sudah sampai dan diterima di meja.
     */
    public function confirmOrderReceived()
    {
        if (!$this->order || $this->order->status === 'completed') {
            return;
        }

        $this->order->update([
            'status' => 'completed'
        ]);

        // Pastikan status meja tetap 'occupied' selama pelanggan masih berada di meja
        if ($this->order->table && $this->order->table->status !== 'occupied') {
            $this->order->table->update(['status' => 'occupied']);
            \App\Events\TableUpdated::dispatch($this->order->table);
        }

        // Tambahkan poin loyalitas jika ada
        if ($this->order->customer_id && $this->order->points_earned > 0) {
            $customer = \App\Models\Customer::find($this->order->customer_id);
            if ($customer) {
                $customer->increment('points', $this->order->points_earned);
            }
        }

        \App\Events\OrderUpdated::dispatch($this->order);

        $this->order->refresh();
        session()->flash('message', 'Terima kasih telah mengonfirmasi pesanan Anda! Selamat menikmati hidangan 🙏');
        $this->dispatch('order-confirmed');
    }

    /**
     * Pelanggan menekan tombol "Selesai Makan & Tinggalkan Meja".
     */
    public function leaveTable()
    {
        if ($this->order && $this->order->table) {
            $this->order->table->update(['status' => 'available']);
            \App\Events\TableUpdated::dispatch($this->order->table);
        }

        Session::forget('table_id');
        Session::forget('table_number');
        Session::forget('cart');

        session()->flash('message', 'Terima kasih telah berkunjung ke Rumpo Cafe! Meja Anda telah dikosongkan. Sampai jumpa kembali 🙏');

        return $this->redirect(route('welcome'), navigate: true);
    }

    #[On('echo:orders,OrderUpdated')]
    public function refreshOrder()
    {
        $this->order->refresh();
        $this->dispatch('order-updated', status: $this->order->status);
    }

    public function render()
    {
        return view('livewire.customer.order-status');
    }
}
