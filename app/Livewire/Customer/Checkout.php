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
    public $subtotal = 0;
    public $is_occupied = false;
    
    // Promo properties
    public $promoCodeInput = '';
    public $appliedPromo = null;
    public $discountAmount = 0;

    public function mount()
    {
        if (empty(session()->get('cart'))) {
            return redirect()->route('welcome');
        }
        $this->cart = session()->get('cart', []);
        $this->subtotal = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        $this->total = $this->subtotal;

        // Load Table Number from Session
        $this->table_number = session('table_number', '');

        if ($this->table_number) {
            $table = \App\Models\Table::where('table_number', $this->table_number)->first();
            if ($table && ($table->status === 'occupied' || $table->orders()->whereNotIn('status', ['completed', 'cancelled'])->exists())) {
                $this->is_occupied = true;
            }
        }

        // Load QRIS Image
        $qris = \App\Models\Qris::where('is_active', true)->first();
        if ($qris) {
            $this->qris_image = $qris->image_path;
        }
    }

    protected $rules = [
        'table_number' => 'required|string|max:50',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'payment_proof' => 'required|image|max:51200',
    ];

    public function applyPromo()
    {
        $this->resetErrorBag('promoCodeInput');
        
        $promo = \App\Models\Promotion::where('code', strtoupper($this->promoCodeInput))
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (!$promo) {
            $this->addError('promoCodeInput', 'Kode promo tidak valid atau kadaluarsa.');
            return;
        }

        if ($this->subtotal < $promo->min_purchase) {
            $this->addError('promoCodeInput', 'Minimal pembelian untuk promo ini adalah Rp ' . number_format($promo->min_purchase, 0, ',', '.'));
            return;
        }

        $this->appliedPromo = $promo;
        
        if ($promo->type === 'percentage') {
            $this->discountAmount = ($this->subtotal * $promo->value) / 100;
        } else {
            $this->discountAmount = $promo->value;
        }

        if ($this->discountAmount > $this->subtotal) {
            $this->discountAmount = $this->subtotal;
        }

        $this->total = $this->subtotal - $this->discountAmount;
        $this->promoCodeInput = '';
        session()->flash('promo_message', 'Kode Promo berhasil digunakan!');
    }

    public function removePromo()
    {
        $this->appliedPromo = null;
        $this->discountAmount = 0;
        $this->total = $this->subtotal;
    }

    public function processCheckout()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Find or create table
            $table = \App\Models\Table::firstOrCreate(
                ['table_number' => $this->table_number],
                ['status' => 'occupied']
            );
            $table->update(['status' => 'occupied']);

            $order = Order::create([
                'table_id' => $table->id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'total_amount' => $this->total,
                'status' => 'waiting_verification',
                'promotion_id' => $this->appliedPromo ? $this->appliedPromo->id : null,
                'discount_amount' => $this->discountAmount
            ]);

            // Create Order Details
            foreach ($this->cart as $cartKey => $item) {
                // Ekstrak menu_id jika cartKey menggunakan format baru {id}_{hash}
                $fallbackMenuId = is_numeric($cartKey) ? $cartKey : explode('_', $cartKey)[0];
                
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'] ?? $fallbackMenuId,
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            // Upload Payment Proof
            $proofPath = $this->payment_proof->store('payments', 'public');

            // Create Payment
            Payment::create([
                'order_id' => $order->id,
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
