<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'table_id', 'customer_name', 'customer_phone', 'customer_id',
        'total_amount', 'status',
        'points_earned', 'points_redeemed',
        'promotion_id', 'discount_amount'
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(EventPromotion::class, 'promotion_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
