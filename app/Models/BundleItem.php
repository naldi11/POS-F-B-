<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleItem extends Model
{
    protected $fillable = ['bundle_id', 'menu_id', 'quantity'];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
