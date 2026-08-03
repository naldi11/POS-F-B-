<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Qris extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'is_active'];
}
