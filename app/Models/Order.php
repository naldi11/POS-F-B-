<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['table_id', 'customer_name', 'customer_phone', 'total_amount', 'status', 'promotion_id', 'discount_amount'])]
class Order extends Model
{
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
