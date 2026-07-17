<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Order;
use Carbon\Carbon;

class Overview extends Component
{
    public function render()
    {
        $totalMenus = Menu::count();
        
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = Order::whereDate('created_at', Carbon::today())
                             ->where('status', 'completed')
                             ->sum('total_amount');

        return view('livewire.admin.overview', [
            'totalMenus' => $totalMenus,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
        ]);
    }
}
