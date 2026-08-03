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

    public $customerPoints = 0;
    public $customer_id = null;
    public $usePoints = false;
    public $pointsDiscount = 0;
    
    public $loyaltyPointsPer1000 = 1;
    public $loyaltyPointValue = 10;

    public function mount()
    {
        if (empty(session()->get('cart'))) {
            return redirect()->route('welcome');
        }
        $this->cart = session()->get('cart', []);
        $this->subtotal = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        
        $this->loyaltyPointsPer1000 = \App\Models\Setting::where('key', 'loyalty_points_per_1000_rupiah')->value('value') ?? 1;
        $this->loyaltyPointValue = \App\Models\Setting::where('key', 'loyalty_point_value')->value('value') ?? 10;
        
        $this->calculateTotal();

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

    public function calculateTotal()
    {
        $this->total = $this->subtotal - $this->discountAmount;
        
        if ($this->usePoints && $this->customerPoints > 0) {
            $this->pointsDiscount = $this->customerPoints * $this->loyaltyPointValue;
            if ($this->pointsDiscount > $this->total) {
                $this->pointsDiscount = $this->total; // don't exceed total
            }
        } else {
            $this->pointsDiscount = 0;
        }
        
        $this->total -= $this->pointsDiscount;
    }

    public function checkPoints()
    {
        if (empty($this->customer_phone)) {
            $this->addError('customer_phone', 'Masukkan nomor HP terlebih dahulu.');
            return;
        }

        $customer = \App\Models\Customer::where('phone', $this->customer_phone)->first();
        if ($customer) {
            $this->customerPoints = $customer->points;
            $this->customer_id = $customer->id;
            session()->flash('points_message', 'Anda memiliki ' . number_format($this->customerPoints, 0, ',', '.') . ' Poin.');
        } else {
            $this->customerPoints = 0;
            $this->customer_id = null;
            session()->flash('points_message', 'Anda belum memiliki poin. Daftar pesanan ini akan memberi Anda poin pertama!');
        }
        $this->usePoints = false;
        $this->calculateTotal();
    }

    public function togglePoints()
    {
        $this->calculateTotal();
    }

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

        $this->promoCodeInput = '';
        $this->calculateTotal();
        session()->flash('promo_message', 'Kode Promo berhasil digunakan!');
    }

    public function removePromo()
    {
        $this->appliedPromo = null;
        $this->discountAmount = 0;
        $this->calculateTotal();
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

            // Find or create customer
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $this->customer_phone],
                ['name' => $this->customer_name, 'points' => 0]
            );

            // If name is different, update it
            if ($customer->name !== $this->customer_name) {
                $customer->update(['name' => $this->customer_name]);
            }

            $pointsEarned = floor($this->total / 1000) * $this->loyaltyPointsPer1000;
            $pointsRedeemed = ($this->usePoints && $this->pointsDiscount > 0) ? floor($this->pointsDiscount / $this->loyaltyPointValue) : 0;

            if ($pointsRedeemed > 0) {
                $customer->decrement('points', $pointsRedeemed);
            }

            $order = Order::create([
                'table_id' => $table->id,
                'customer_id' => $customer->id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'total_amount' => $this->total,
                'status' => 'waiting_verification',
                'promotion_id' => $this->appliedPromo ? $this->appliedPromo->id : null,
                'discount_amount' => $this->discountAmount,
                'points_earned' => $pointsEarned,
                'points_redeemed' => $pointsRedeemed,
            ]);

            // Create Order Details
            foreach ($this->cart as $cartKey => $item) {
                $isBundle = $item['is_bundle'] ?? false;
                
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $isBundle ? null : $item['menu_id'],
                    'bundle_id' => $isBundle ? $item['bundle_id'] : null,
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
            session()->flash('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi. ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
