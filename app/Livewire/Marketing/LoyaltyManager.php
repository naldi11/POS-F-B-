<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Customer;
use Livewire\WithPagination;

class LoyaltyManager extends Component
{
    use WithPagination;

    public $points_per_1000 = 1;
    public $point_value = 10;
    public $search = '';

    protected $rules = [
        'points_per_1000' => 'required|numeric|min:0',
        'point_value' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->points_per_1000 = Setting::where('key', 'loyalty_points_per_1000_rupiah')->value('value') ?? 1;
        $this->point_value = Setting::where('key', 'loyalty_point_value')->value('value') ?? 10;
    }

    public function saveSettings()
    {
        $this->validate();

        Setting::updateOrCreate(['key' => 'loyalty_points_per_1000_rupiah'], ['value' => $this->points_per_1000]);
        Setting::updateOrCreate(['key' => 'loyalty_point_value'], ['value' => $this->point_value]);

        session()->flash('message', 'Pengaturan sistem loyalty berhasil disimpan.');
    }

    public function render()
    {
        $customers = Customer::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
        })
        ->orderBy('points', 'desc')
        ->paginate(20);

        return view('livewire.marketing.loyalty-manager', [
            'customers' => $customers
        ]);
    }
}
