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
        // Validasi jika promo memiliki minimal pembelian tapi subtotal tidak cukup
        if ($this->appliedPromo) {
            $minPurchase = (float) ($this->appliedPromo->min_purchase ?? 0);
            if ($minPurchase > 0 && $this->subtotal < $minPurchase) {
                $this->removePromo();
                $this->addError('promoCodeInput', 'Promo dibatalkan karena total belanja kurang dari batas minimal Rp ' . number_format($minPurchase, 0, ',', '.'));
            }
        }

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
        
        $code = strtoupper(trim($this->promoCodeInput));
        if (empty($code)) {
            $this->addError('promoCodeInput', 'Masukkan kode promo terlebih dahulu.');
            return;
        }

        // 1. Cek di tabel Promotion (Voucher Kode Diskon Marketing)
        $promo = \App\Models\Promotion::where('code', $code)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->first();

        $isEvent = false;

        // 2. Jika tidak ditemukan, cek di tabel EventPromotion (Promo Event / Banner)
        if (!$promo) {
            $promo = \App\Models\EventPromotion::where('coupon_code', $code)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->first();
            if ($promo) {
                $isEvent = true;
            }
        }

        if (!$promo) {
            $this->addError('promoCodeInput', 'Kode promo tidak valid atau kadaluarsa.');
            return;
        }

        // Validasi kuota pemakaian
        if (!$isEvent) {
            if (!is_null($promo->max_uses) && $promo->used_count >= $promo->max_uses) {
                $this->addError('promoCodeInput', 'Mohon maaf, kuota penggunaan kode promo ini sudah habis.');
                return;
            }
        } else {
            if (!is_null($promo->usage_limit) && $promo->used_count >= $promo->usage_limit) {
                $this->addError('promoCodeInput', 'Mohon maaf, kuota penggunaan kode promo ini sudah habis.');
                return;
            }
        }

        // Validasi Minimal Order / Minimal Pembelian (min_purchase)
        $minPurchase = (float) ($promo->min_purchase ?? 0);
        if ($minPurchase > 0 && $this->subtotal < $minPurchase) {
            $this->addError('promoCodeInput', 'Minimal pembelian untuk promo ini adalah Rp ' . number_format($minPurchase, 0, ',', '.') . '. Total belanja Anda: Rp ' . number_format($this->subtotal, 0, ',', '.'));
            return;
        }

        // Hitung nominal diskon
        if (!$isEvent) {
            if ($promo->type === 'percentage') {
                $this->discountAmount = ($this->subtotal * (float) $promo->value) / 100;
                if (!is_null($promo->max_discount) && (float) $promo->max_discount > 0 && $this->discountAmount > (float) $promo->max_discount) {
                    $this->discountAmount = (float) $promo->max_discount;
                }
            } else {
                $this->discountAmount = (float) $promo->value;
            }
        } else {
            $this->discountAmount = ($this->subtotal * (float) $promo->discount_percentage) / 100;
        }

        if ($this->discountAmount > $this->subtotal) {
            $this->discountAmount = $this->subtotal;
        }

        $this->appliedPromo = (object) [
            'id' => $promo->id,
            'code' => $isEvent ? $promo->coupon_code : $promo->code,
            'is_event' => $isEvent,
            'min_purchase' => $minPurchase,
        ];

        $this->promoCodeInput = '';
        $this->calculateTotal();
        session()->flash('promo_message', 'Kode Promo ' . ($isEvent ? $promo->coupon_code : $promo->code) . ' berhasil digunakan!');
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

        // Cek apakah meja sedang digunakan
        if ($this->is_occupied) {
            $this->addError('table_number', 'Meja ' . $this->table_number . ' saat ini masih terisi. Silakan hubungi kasir.');
            session()->flash('error', 'Meja ' . $this->table_number . ' saat ini masih digunakan oleh pelanggan lain. Silakan lapor ke kasir untuk konfirmasi meja kosong.');
            return;
        }

        DB::beginTransaction();

        try {
            // Re-validate and lock promo if applied
            $orderPromoId = null;
            if ($this->appliedPromo) {
                $isEvent = $this->appliedPromo->is_event ?? false;
                $promoId = $this->appliedPromo->id;
                $minPurchase = (float) ($this->appliedPromo->min_purchase ?? 0);

                if ($minPurchase > 0 && $this->subtotal < $minPurchase) {
                    DB::rollBack();
                    $this->removePromo();
                    $this->addError('promoCodeInput', 'Minimal pembelian untuk promo ini tidak terpenuhi (Min. Rp ' . number_format($minPurchase, 0, ',', '.') . ').');
                    return;
                }

                if ($isEvent) {
                    $promo = \App\Models\EventPromotion::where('id', $promoId)->lockForUpdate()->first();
                    if (!$promo || (!is_null($promo->usage_limit) && $promo->used_count >= $promo->usage_limit)) {
                        DB::rollBack();
                        $this->removePromo();
                        $this->addError('promoCodeInput', 'Mohon maaf, kuota promo baru saja habis. Silakan checkout ulang tanpa promo.');
                        return;
                    }
                    $promo->increment('used_count');
                    $orderPromoId = $promo->id;
                } else {
                    $promo = \App\Models\Promotion::where('id', $promoId)->lockForUpdate()->first();
                    if (!$promo || (!is_null($promo->max_uses) && $promo->used_count >= $promo->max_uses)) {
                        DB::rollBack();
                        $this->removePromo();
                        $this->addError('promoCodeInput', 'Mohon maaf, kuota promo baru saja habis. Silakan checkout ulang tanpa promo.');
                        return;
                    }
                    $promo->increment('used_count');
                    $orderPromoId = null; // Disimpan tanpa melanggar FK event_promotions jika FK masih aktif
                }
            }

            // Find or create table
            $table = \App\Models\Table::where('table_number', $this->table_number)->first();
            if (!$table) {
                $table = \App\Models\Table::create([
                    'table_number' => $this->table_number,
                    'status' => 'occupied'
                ]);
            } else {
                if ($table->status === 'occupied' || $table->orders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
                    DB::rollBack();
                    $this->is_occupied = true;
                    $this->addError('table_number', 'Meja ' . $this->table_number . ' saat ini masih terisi. Silakan hubungi kasir.');
                    session()->flash('error', 'Meja ' . $this->table_number . ' saat ini masih digunakan oleh pelanggan lain. Silakan lapor ke kasir untuk konfirmasi meja kosong.');
                    return;
                }
                $table->update(['status' => 'occupied']);
            }

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
                'promotion_id' => $orderPromoId,
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

            // Broadcast NewOrder & TableUpdated
            \App\Events\NewOrder::dispatch($order);
            \App\Events\TableUpdated::dispatch($table);

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
