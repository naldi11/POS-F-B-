<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'menu_id', 'bundle_id', 'quantity', 'notes'])]
class OrderDetail extends Model
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function getSubtotalAttribute()
    {
        if ($this->bundle_id) {
            return $this->bundle->price * $this->quantity;
        }
        
        return $this->menu->price * $this->quantity;
    }
}
