<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'used_count',
        'min_purchase',
        'max_discount',
        'valid_from',
        'valid_until',
        'is_active',
    ];
}
