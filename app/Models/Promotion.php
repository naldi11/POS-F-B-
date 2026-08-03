<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'valid_from',
        'valid_until',
        'is_active',
    ];
}
