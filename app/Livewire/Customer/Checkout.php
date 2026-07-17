<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.customer')]
class Checkout extends Component
{
    use WithFileUploads;

    public $table_number;
    public $customer_name;
    public $customer_phone;
    public $payment_proof;
    public $qris_image;
    public $cart = [];
    public $total = 0;

    public function mount()
    {
        if (empty(session()->get('cart'))) {
            return redirect()->route('welcome');
        }
        $this->cart = session()->get('cart', []);
        $this->total = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        // Load QRIS Image
        $setting = \App\Models\Setting::where('key', 'qris_image')->first();
        if ($setting) {
            $this->qris_image = $setting->value;
        }
    }

    protected $rules = [
        'table_number' => 'required|string|max:50',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'payment_proof' => 'required|image|max:2048',
    ];

    public function processCheckout()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Find or create table
            $table = \App\Models\Table::firstOrCreate(
                ['table_number' => $this->table_number],
                ['status' => 'available']
            );

            // Create Order
            $orderNumber = 'ORD-' . date('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            $invoiceNumber = 'INV-' . date('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_number' => $invoiceNumber,
                'table_id' => $table->id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'total_amount' => $this->total,
                'status' => 'waiting_verification'
            ]);

            // Create Order Details
            foreach ($this->cart as $cartKey => $item) {
                // Ekstrak menu_id jika cartKey menggunakan format baru {id}_{hash}
                $fallbackMenuId = is_numeric($cartKey) ? $cartKey : explode('_', $cartKey)[0];
                
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'] ?? $fallbackMenuId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            // Upload Payment Proof
            $proofPath = $this->payment_proof->store('payments', 'public');

            // Create Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'qris',
                'proof_image' => $proofPath,
                'status' => 'pending'
            ]);

            DB::commit();

            // Broadcast NewOrder
            \App\Events\NewOrder::dispatch($order);

            // Clear session cart
            session()->forget('cart');

            // Redirect to status page
            return redirect()->route('customer.order-status', ['id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
