<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'theme',
        'headline',
        'description',
        'banner_image',
        'coupon_code',
        'usage_limit',
        'used_count',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_percentage' => 'float',
    ];

    /**
     * Scope untuk mendapatkan event yang sedang aktif berdasarkan rentang tanggal
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }
}
