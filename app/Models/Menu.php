<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id', 'name', 'description', 'price', 'image', 'is_available', 'best_seller_status'])]
class Menu extends Model
{
    protected $appends = ['is_best_seller'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getIsBestSellerAttribute(): bool
    {
        if ($this->best_seller_status === 'yes') return true;
        if ($this->best_seller_status === 'no') return false;

        // Auto mode: check if it's in the top 3 most sold items
        $topMenus = cache()->remember('top_menus', 3600, function () {
            return \App\Models\OrderDetail::select('menu_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('menu_id')
                ->orderByDesc('total_sold')
                ->limit(3)
                ->pluck('menu_id')
                ->toArray();
        });

        return in_array($this->id, $topMenus);
    }
}
