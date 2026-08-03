<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Promotion;
use App\Models\Order;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $activePromosCount = Promotion::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
            })->count();

        $totalPromosUsed = Order::whereNotNull('promotion_id')->count();
        $totalDiscountGiven = Order::sum('discount_amount');

        return view('livewire.marketing.dashboard', [
            'activePromosCount' => $activePromosCount,
            'totalPromosUsed' => $totalPromosUsed,
            'totalDiscountGiven' => $totalDiscountGiven,
            'recentPromos' => Promotion::latest()->take(5)->get()
        ]);
    }
}
